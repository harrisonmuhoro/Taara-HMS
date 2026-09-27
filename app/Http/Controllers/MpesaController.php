<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\MpesaService;
use App\Models\MpesaTransaction;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\Reservation;
use App\Models\Payment;
use App\Models\PaymentMethod;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class MpesaController extends Controller
{
    protected $mpesaService;

    public function __construct(MpesaService $mpesaService)
    {
        $this->mpesaService = $mpesaService;
    }

    /**
     * Initiate an STK Push
     */
    public function initiateStkPush(Request $request)
    {
        $validated = $request->validate([
            'phone'          => ['required', 'string', 'regex:/^(?:\+?254|0)\d{9}$/'],
            'amount'         => ['required', 'numeric', 'min:1'],
            'invoice_id'     => ['nullable', 'exists:invoices,id'],
            'order_id'       => ['nullable', 'exists:orders,id'],
            'reservation_id' => ['nullable', 'exists:reservations,id'],
        ]);

        $reservation = ! empty($validated['reservation_id'])
            ? Reservation::findOrFail($validated['reservation_id'])
            : null;

        if ($reservation) {
            if ($reservation->status === 'CANCELLED') {
                return response()->json(['success' => false, 'message' => 'Cancelled reservations cannot receive deposits.'], 422);
            }

            $balanceDue = max(0, round((float) $reservation->total_amount - (float) $reservation->deposit_amount, 2));
            if ((float) $validated['amount'] > $balanceDue) {
                return response()->json([
                    'success' => false,
                    'message' => 'The deposit cannot exceed the outstanding balance of KES ' . number_format($balanceDue, 2) . '.',
                ], 422);
            }
        }

        $accountRef = 'Taara Hotel';
        $desc = 'Room Booking Payment';

        if (! empty($validated['order_id'])) {
            $order = Order::find($validated['order_id']);
            $accountRef = $order ? $order->order_number : 'Taara POS';
            $desc = 'Restaurant Order Payment';
        } elseif ($reservation) {
            $accountRef = $reservation ? $reservation->reservation_number : 'Booking Deposit';
            $desc = 'Advance Booking Deposit';
        }

        try {
            $response = $this->mpesaService->stkPush(
                $validated['phone'],
                $validated['amount'],
                $accountRef,
                $desc
            );

            if (isset($response['ResponseCode']) && $response['ResponseCode'] == '0') {
                MpesaTransaction::create([
                    'invoice_id'          => $validated['invoice_id'] ?? null,
                    'order_id'            => $validated['order_id'] ?? null,
                    'reservation_id'      => $validated['reservation_id'] ?? null,
                    'transaction_type'    => 'STK_PUSH',
                    'merchant_request_id' => $response['MerchantRequestID'],
                    'checkout_request_id' => $response['CheckoutRequestID'],
                    'phone_number'        => $validated['phone'],
                    'amount'              => $validated['amount'],
                    'status'              => 'pending',
                ]);

                return response()->json([
                    'success'             => true,
                    'message'             => 'STK Push initiated successfully. Please check your phone.',
                    'checkout_request_id' => $response['CheckoutRequestID'],
                    'data'                => $response,
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => $response['errorMessage'] ?? $response['ResponseDescription'] ?? 'Failed to initiate STK push',
                'data'    => $response,
            ], 400);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Handle STK Push Callback from Safaricom
     */
    public function stkCallback(Request $request)
    {
        $callbackData = $request->input('Body.stkCallback');

        if (!$callbackData) {
            return response()->json(['success' => false, 'message' => 'Invalid Callback Data'], 400);
        }

        $resultCode       = $callbackData['ResultCode'];
        $resultDesc       = $callbackData['ResultDesc'];
        $checkoutRequestId = $callbackData['CheckoutRequestID'];

        $transaction = MpesaTransaction::where('checkout_request_id', $checkoutRequestId)->first();

        if (!$transaction) {
            Log::warning('M-Pesa STK Callback received for unknown transaction: ' . $checkoutRequestId);
            return response()->json(['success' => true]);
        }

        if ($resultCode == 0) {
            // Payment successful — extract metadata
            $callbackItems    = $callbackData['CallbackMetadata']['Item'] ?? [];
            $mpesaReceiptNumber = '';
            $transactionDate  = now();
            $paidAmount       = $transaction->amount;

            foreach ($callbackItems as $item) {
                match ($item['Name']) {
                    'MpesaReceiptNumber' => $mpesaReceiptNumber = $item['Value'],
                    'TransactionDate'    => $transactionDate = $item['Value'],
                    'Amount'             => $paidAmount = $item['Value'],
                    default              => null,
                };
            }

            DB::transaction(function () use ($transaction, $mpesaReceiptNumber, $resultDesc, $request, $paidAmount) {
                $transaction = MpesaTransaction::query()->lockForUpdate()->findOrFail($transaction->id);

                // Safaricom may retry callbacks. A completed transaction must never be applied twice.
                if ($transaction->status === 'completed') {
                    return;
                }

                // 1. Update the MpesaTransaction record
                $transaction->update([
                    'status'       => 'completed',
                    'transaction_id' => $mpesaReceiptNumber,
                    'result_desc'  => $resultDesc,
                    'raw_response' => json_encode($request->all()),
                ]);

                // 2. If linked to an invoice, create a Payment and recalculate balance
                if ($transaction->invoice_id) {
                    $invoice = Invoice::find($transaction->invoice_id);

                    if ($invoice) {
                        $mpesaMethod = PaymentMethod::where('name', 'LIKE', '%pesa%')
                            ->orWhere('name', 'LIKE', '%mobile%')
                            ->first();

                        // Get a system user to record as the receiver (required by DB)
                        $systemUser = \App\Models\User::first();

                        Payment::create([
                            'branch_id'         => $invoice->branch_id,
                            'invoice_id'        => $invoice->id,
                            'guest_id'          => $invoice->guest_id,
                            'payment_method_id' => $mpesaMethod?->id,
                            'amount'            => $paidAmount,
                            'reference_number'  => $mpesaReceiptNumber,
                            'transaction_date'  => now(),
                            'received_by'       => $systemUser?->id,
                            'status'            => 'COMPLETED',
                            'notes'             => 'M-Pesa STK Push — Receipt: ' . $mpesaReceiptNumber,
                        ]);

                        $invoice->recalculateTotals();

                        Log::info("Invoice #{$invoice->id} updated after M-Pesa payment of KES {$paidAmount}. Receipt: {$mpesaReceiptNumber}");
                    }
                }

                // 3. If linked to an order, update order status to completed and append receipt
                if ($transaction->order_id) {
                    $order = Order::find($transaction->order_id);

                    if ($order) {
                        $orderNote = 'Paid via M-Pesa. Receipt: ' . $mpesaReceiptNumber;
                        $order->update([
                            'status'         => 'completed',
                            'payment_method' => 'mpesa',
                            'completed_at'   => now(),
                            'notes'          => trim(($order->notes ? $order->notes . ' | ' : '') . $orderNote),
                        ]);

                        Log::info("Order #{$order->id} ({$order->order_number}) updated after M-Pesa payment of KES {$paidAmount}. Receipt: {$mpesaReceiptNumber}");
                    }
                }

                // 4. If linked to a reservation, update deposit_amount and transition status if pending
                if ($transaction->reservation_id) {
                    $reservation = Reservation::find($transaction->reservation_id);

                    if ($reservation) {
                        $newDeposit = (float) $reservation->deposit_amount + (float) $paidAmount;
                        $specialRequests = trim(($reservation->special_requests ? $reservation->special_requests . ' | ' : '') . 'M-Pesa Deposit: KES ' . number_format($paidAmount, 2) . ' (Receipt: ' . $mpesaReceiptNumber . ')');

                        $updateData = [
                            'deposit_amount'   => $newDeposit,
                            'special_requests' => $specialRequests,
                        ];

                        if ($reservation->status === 'PENDING') {
                            $updateData['status'] = 'CONFIRMED';
                        }

                        $reservation->update($updateData);

                        Log::info("Reservation #{$reservation->id} ({$reservation->reservation_number}) deposit updated to KES {$newDeposit} via M-Pesa. Receipt: {$mpesaReceiptNumber}");
                    }
                }
            });

        } else {
            // Payment failed or cancelled
            $transaction->update([
                'status'       => 'failed',
                'result_desc'  => $resultDesc,
                'raw_response' => json_encode($request->all()),
            ]);

            if ($transaction->order_id) {
                $order = Order::find($transaction->order_id);
                if ($order && $order->status === 'pending') {
                    $order->update([
                        'notes' => trim(($order->notes ? $order->notes . ' | ' : '') . 'M-Pesa payment failed: ' . $resultDesc),
                    ]);
                }
            }
        }

        return response()->json(['success' => true]);
    }

    /**
     * Query transaction status by CheckoutRequestID
     */
    public function queryStatus($checkoutRequestId)
    {
        $transaction = MpesaTransaction::where('checkout_request_id', $checkoutRequestId)
            ->latest()
            ->first();

        if (!$transaction) {
            return response()->json([
                'success' => false,
                'status'  => 'not_found',
                'message' => 'Transaction not found',
            ], 404);
        }

        return response()->json([
            'success'        => true,
            'status'         => $transaction->status, // pending, completed, failed
            'transaction_id' => $transaction->transaction_id,
            'result_desc'    => $transaction->result_desc,
            'amount'         => $transaction->amount,
            'order_id'       => $transaction->order_id,
            'invoice_id'     => $transaction->invoice_id,
            'reservation_id' => $transaction->reservation_id,
        ]);
    }

    /**
     * Handle C2B Validation
     */
    public function c2bValidation(Request $request)
    {
        Log::info('C2B Validation: ', $request->all());
        return response()->json(['ResultCode' => 0, 'ResultDesc' => 'Accepted']);
    }

    /**
     * Handle C2B Confirmation
     */
    public function c2bConfirmation(Request $request)
    {
        Log::info('C2B Confirmation: ', $request->all());

        MpesaTransaction::create([
            'transaction_type' => 'C2B',
            'transaction_id'   => $request->input('TransID'),
            'phone_number'     => $request->input('MSISDN'),
            'amount'           => $request->input('TransAmount'),
            'status'           => 'completed',
            'result_desc'      => 'Confirmed via C2B',
            'raw_response'     => json_encode($request->all()),
        ]);

        return response()->json(['ResultCode' => 0, 'ResultDesc' => 'Success']);
    }

    /**
     * Register C2B URLs manually (Utility endpoint)
     */
    public function registerUrls()
    {
        try {
            $response = $this->mpesaService->registerC2BUrls(
                env('MPESA_C2B_VALIDATION_URL'),
                env('MPESA_C2B_CONFIRMATION_URL')
            );
            return response()->json($response);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
