<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Reservations\BookingDepositController;
use App\Models\Invoice;
use App\Models\MpesaTransaction;
use App\Models\Order;
use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Models\Reservation;
use App\Models\User;
use App\Services\MpesaService;
use App\Services\ReservationService;
use App\Support\Money;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class MpesaController extends Controller
{
    protected $mpesaService;

    public function __construct(
        MpesaService $mpesaService,
        protected ReservationService $reservationService,
    ) {
        $this->mpesaService = $mpesaService;
    }

    /**
     * Initiate an STK Push
     */
    public function initiateStkPush(Request $request)
    {
        $user = $request->user();
        $branchId = $user->branch_id;
        $isSuper = $user->isSuperAdmin();

        $validated = $request->validate([
            'phone' => ['required', 'string', 'regex:/^(?:\+?254|0)\d{9}$/'],
            'amount' => ['required', 'numeric', 'decimal:0,2', 'min:1'],
            'invoice_id' => [
                'nullable',
                'required_without_all:order_id,reservation_id',
                Rule::exists('invoices', 'id')->when(! $isSuper, fn ($r) => $r->where('branch_id', $branchId)),
            ],
            'order_id' => [
                'nullable',
                'required_without_all:invoice_id,reservation_id',
                Rule::exists('orders', 'id')->when(! $isSuper, fn ($r) => $r->where('branch_id', $branchId)),
            ],
            'reservation_id' => [
                'nullable',
                'required_without_all:invoice_id,order_id',
                Rule::exists('reservations', 'id')->when(! $isSuper, fn ($r) => $r->where('branch_id', $branchId)),
            ],
        ]);
        $branchIds = collect();

        $reservation = ! empty($validated['reservation_id'])
            ? Reservation::findOrFail($validated['reservation_id'])
            : null;

        if ($reservation) {
            $branchIds->push($reservation->branch_id);
        }

        $invoice = ! empty($validated['invoice_id']) ? Invoice::findOrFail($validated['invoice_id']) : null;
        if ($invoice) {
            $branchIds->push($invoice->branch_id);
            abort_unless($user->isSuperAdmin() || bccomp((string) $validated['amount'], (string) $invoice->balance_due, 2) <= 0, 422);
        }

        $order = ! empty($validated['order_id']) ? Order::findOrFail($validated['order_id']) : null;
        if ($order) {
            $branchIds->push($order->branch_id);
            abort_unless($user->isSuperAdmin() || bccomp((string) $validated['amount'], (string) $order->total, 2) <= 0, 422);
        }

        abort_unless($user->isSuperAdmin() || $branchIds->every(fn ($branchId) => (int) $branchId === (int) $user->branch_id), 403);

        if ($reservation) {
            if ($reservation->status === 'CANCELLED') {
                return response()->json(['success' => false, 'message' => 'Cancelled reservations cannot receive deposits.'], 422);
            }

            $paymentLimitMinor = Money::toMinor($reservation->deposit_paid
                ? $reservation->balance_due
                : $reservation->deposit_amount);
            $amountMinor = Money::toMinor($validated['amount']);

            if (! $reservation->deposit_paid && $amountMinor !== $paymentLimitMinor) {
                return response()->json([
                    'success' => false,
                    'message' => 'The initial room deposit must be exactly KES '.Money::fromMinor($paymentLimitMinor).'.',
                ], 422);
            }

            if ($amountMinor > $paymentLimitMinor) {
                return response()->json([
                    'success' => false,
                    'message' => 'The payment cannot exceed the outstanding balance of KES '.Money::fromMinor($paymentLimitMinor).'.',
                ], 422);
            }
        }

        $accountRef = 'Taara Hotel';
        $desc = 'Room Booking Payment';

        if (! empty($validated['order_id'])) {
            $accountRef = $order ? $order->order_number : 'Taara POS';
            $desc = 'Restaurant Order Payment';
        } elseif ($reservation) {
            $accountRef = $reservation ? $reservation->reservation_number : 'Booking Deposit';
            $desc = $reservation->deposit_paid ? 'Room Balance Payment' : 'Advance Room Deposit';
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
                    'invoice_id' => $validated['invoice_id'] ?? null,
                    'order_id' => $validated['order_id'] ?? null,
                    'reservation_id' => $validated['reservation_id'] ?? null,
                    'transaction_type' => 'STK_PUSH',
                    'merchant_request_id' => $response['MerchantRequestID'],
                    'checkout_request_id' => $response['CheckoutRequestID'],
                    'phone_number' => $validated['phone'],
                    'amount' => $validated['amount'],
                    'status' => 'pending',
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'STK Push initiated successfully. Please check your phone.',
                    'checkout_request_id' => $response['CheckoutRequestID'],
                    'data' => $response,
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => $response['errorMessage'] ?? $response['ResponseDescription'] ?? 'Failed to initiate STK push',
                'data' => $response,
            ], 400);

        } catch (\Exception $e) {
            report($e);

            return response()->json(['success' => false, 'message' => 'Unable to initiate the payment.'], 500);
        }
    }

    /**
     * Handle STK Push Callback from Safaricom
     */
    public function stkCallback(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'Body.stkCallback' => ['required', 'array'],
            'Body.stkCallback.ResultCode' => ['required', 'integer'],
            'Body.stkCallback.ResultDesc' => ['required', 'string', 'max:255'],
            'Body.stkCallback.CheckoutRequestID' => ['required', 'string', 'max:100'],
            'Body.stkCallback.CallbackMetadata.Item' => ['required_if:Body.stkCallback.ResultCode,0', 'array'],
            'Body.stkCallback.CallbackMetadata.Item.*.Name' => ['required', 'string', 'max:64'],
            'Body.stkCallback.CallbackMetadata.Item.*.Value' => ['nullable'],
        ]);

        if ($validator->fails()) {
            Log::warning('Rejected malformed M-Pesa STK callback.', ['errors' => $validator->errors()->toArray()]);

            return response()->json(['success' => false, 'message' => 'Invalid callback payload.'], 400);
        }

        $callbackData = $request->input('Body.stkCallback');

        $resultCode = $callbackData['ResultCode'];
        $resultDesc = $callbackData['ResultDesc'];
        $checkoutRequestId = $callbackData['CheckoutRequestID'];

        $transaction = MpesaTransaction::where('checkout_request_id', $checkoutRequestId)->first();

        if (! $transaction) {
            Log::warning('M-Pesa STK Callback received for unknown transaction: '.$checkoutRequestId);

            return response()->json(['success' => true]);
        }

        if ($resultCode == 0) {
            // Payment successful — extract metadata
            $callbackItems = $callbackData['CallbackMetadata']['Item'] ?? [];
            $mpesaReceiptNumber = '';
            $transactionDate = now();
            $paidAmount = $transaction->amount;

            foreach ($callbackItems as $item) {
                if (! isset($item['Name'])) {
                    continue;
                }
                match ($item['Name']) {
                    'MpesaReceiptNumber' => $mpesaReceiptNumber = $item['Value'],
                    'TransactionDate' => $transactionDate = $item['Value'],
                    'Amount' => $paidAmount = $item['Value'],
                    default => null,
                };
            }

            if ($mpesaReceiptNumber === '' || ! is_numeric($paidAmount)
                || bccomp((string) $paidAmount, (string) $transaction->amount, 2) !== 0) {
                Log::warning('Rejected M-Pesa callback with missing or mismatched payment data.', [
                    'checkout_request_id' => $checkoutRequestId,
                ]);

                return response()->json(['success' => false, 'message' => 'Invalid payment data.'], 400);
            }

            DB::transaction(function () use ($transaction, $mpesaReceiptNumber, $resultDesc, $request, $paidAmount) {
                $transaction = MpesaTransaction::query()->lockForUpdate()->findOrFail($transaction->id);

                // Safaricom may retry callbacks. A completed transaction must never be applied twice.
                if ($transaction->status === 'completed') {
                    // A successful STK Query has no receipt number. Preserve the
                    // settlement while allowing a later real callback to replace
                    // the temporary reconciliation reference.
                    if (str_starts_with((string) $transaction->transaction_id, 'STK:')
                        && $mpesaReceiptNumber !== ''
                        && ! str_starts_with($mpesaReceiptNumber, 'STK:')) {
                        $transaction->update([
                            'transaction_id' => $mpesaReceiptNumber,
                            'result_desc' => $resultDesc,
                            'raw_response' => json_encode($request->all()),
                        ]);
                    }

                    return;
                }

                // 1. Update the MpesaTransaction record
                $transaction->update([
                    'status' => 'completed',
                    'transaction_id' => $mpesaReceiptNumber,
                    'result_desc' => $resultDesc,
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
                        $systemUser = User::first();

                        Payment::create([
                            'branch_id' => $invoice->branch_id,
                            'invoice_id' => $invoice->id,
                            'guest_id' => $invoice->guest_id,
                            'payment_method_id' => $mpesaMethod?->id,
                            'amount' => $paidAmount,
                            'reference_number' => $mpesaReceiptNumber,
                            'transaction_date' => now(),
                            'received_by' => $systemUser?->id,
                            'status' => 'COMPLETED',
                            'notes' => 'M-Pesa STK Push — Receipt: '.$mpesaReceiptNumber,
                        ]);

                        $invoice->recalculateTotals();

                        Log::info("Invoice #{$invoice->id} updated after M-Pesa payment of KES {$paidAmount}. Receipt: {$mpesaReceiptNumber}");
                    }
                }

                // 3. If linked to an order, update order status to completed and append receipt
                if ($transaction->order_id) {
                    $order = Order::find($transaction->order_id);

                    if ($order) {
                        $orderNote = 'Paid via M-Pesa. Receipt: '.$mpesaReceiptNumber;
                        $order->update([
                            'status' => 'completed',
                            'payment_method' => 'mpesa',
                            'completed_at' => now(),
                            'notes' => trim(($order->notes ? $order->notes.' | ' : '').$orderNote),
                        ]);

                        Log::info("Order #{$order->id} ({$order->order_number}) updated after M-Pesa payment of KES {$paidAmount}. Receipt: {$mpesaReceiptNumber}");
                    }
                }

                // 4. If linked to a reservation, settle the room deposit and transition status.
                if ($transaction->reservation_id) {
                    $reservation = Reservation::query()->lockForUpdate()->find($transaction->reservation_id);

                    if ($reservation) {
                        if ($reservation->status === 'CANCELLED') {
                            Log::warning("M-Pesa deposit received after reservation #{$reservation->id} was cancelled; refund review is required.");

                            return;
                        }

                        $updateData = [];
                        if (! $reservation->deposit_paid) {
                            $updateData = [
                                'deposit_paid' => true,
                                'deposit_paid_at' => now(),
                                'deposit_receipt_no' => $mpesaReceiptNumber,
                                'deposit_expires_at' => null,
                                'special_requests' => trim(($reservation->special_requests ? $reservation->special_requests.' | ' : '').'M-Pesa Deposit: KES '.number_format($paidAmount, 2).' (Receipt: '.$mpesaReceiptNumber.')'),
                            ];
                        }

                        if ($updateData) {
                            $reservation->update($updateData);
                        }

                        Log::info("Reservation #{$reservation->id} ({$reservation->reservation_number}) received M-Pesa payment of KES {$paidAmount}. Receipt: {$mpesaReceiptNumber}");
                    }
                }
            });

            if ($transaction->reservation_id) {
                $reservation = Reservation::find($transaction->reservation_id);

                if ($reservation?->deposit_paid && $reservation->status === 'PENDING_DEPOSIT') {
                    $this->reservationService->transitionStatus($reservation, 'CONFIRMED');
                    BookingDepositController::notifyFrontDesk(
                        $reservation,
                        'Reservation deposit received',
                        "Deposit received for reservation {$reservation->reservation_number}.",
                    );
                }
            }

        } else {
            // Payment failed or cancelled
            DB::transaction(function () use ($transaction, $resultDesc, $request) {
                $locked = MpesaTransaction::query()->lockForUpdate()->findOrFail($transaction->id);
                if ($locked->status === 'completed') {
                    return;
                }
                $locked->update([
                    'status' => 'failed',
                    'result_desc' => $resultDesc,
                    'raw_response' => json_encode($request->all()),
                ]);
            });

            if ($transaction->order_id) {
                $order = Order::find($transaction->order_id);
                if ($order && $order->status === 'pending') {
                    $order->update([
                        'notes' => trim(($order->notes ? $order->notes.' | ' : '').'M-Pesa payment failed: '.$resultDesc),
                    ]);
                }
            }
        }

        return response()->json(['success' => true]);
    }

    /**
     * Query transaction status by CheckoutRequestID
     */
    public function queryStatus(Request $request, $checkoutRequestId)
    {
        $transaction = MpesaTransaction::where('checkout_request_id', $checkoutRequestId)
            ->latest()
            ->first();

        if (! $transaction) {
            return response()->json([
                'success' => false,
                'status' => 'not_found',
                'message' => 'Transaction not found',
            ], 404);
        }

        abort_unless($this->canAccessTransaction($transaction, $request->user()), 403);

        return response()->json([
            'success' => true,
            'status' => $transaction->status, // pending, completed, failed
            'transaction_id' => $transaction->transaction_id,
            'result_desc' => $transaction->result_desc,
            'amount' => $transaction->amount,
            'order_id' => $transaction->order_id,
            'invoice_id' => $transaction->invoice_id,
            'reservation_id' => $transaction->reservation_id,
        ]);
    }

    /**
     * Handle C2B Validation
     */
    public function c2bValidation(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'TransID' => ['required', 'string', 'max:100'],
            'TransAmount' => ['required', 'numeric', 'min:0.01'],
            'MSISDN' => ['required', 'string', 'max:32'],
        ]);

        if ($validator->fails()) {
            Log::warning('Rejected malformed M-Pesa C2B validation payload.', ['errors' => $validator->errors()->toArray()]);

            return response()->json(['ResultCode' => 1, 'ResultDesc' => 'Invalid payload']);
        }

        Log::info('C2B Validation received.', ['transaction_id' => $request->input('TransID')]);

        return response()->json(['ResultCode' => 0, 'ResultDesc' => 'Accepted']);
    }

    /**
     * Handle C2B Confirmation
     */
    public function c2bConfirmation(Request $request)
    {
        $validated = $request->validate([
            'TransID' => ['required', 'string', 'max:100'],
            'TransAmount' => ['required', 'numeric', 'min:0.01'],
            'MSISDN' => ['required', 'string', 'max:32'],
            'BillRefNumber' => ['nullable', 'string', 'max:100'],
            'AccountReference' => ['nullable', 'string', 'max:100'],
        ]);

        Log::info('C2B Confirmation received.', ['transaction_id' => $validated['TransID']]);

        if (MpesaTransaction::where('transaction_id', $validated['TransID'])->exists()) {
            return response()->json(['ResultCode' => 0, 'ResultDesc' => 'Already processed']);
        }

        $reference = (string) ($validated['BillRefNumber'] ?? $validated['AccountReference'] ?? '');
        $reservationId = str_starts_with($reference, 'DEPOSIT-')
            ? (int) substr($reference, strlen('DEPOSIT-'))
            : null;

        try {
            $transaction = MpesaTransaction::create([
                'reservation_id' => $reservationId ?: null,
                'transaction_type' => 'C2B',
                'transaction_id' => $validated['TransID'],
                'phone_number' => $validated['MSISDN'],
                'amount' => $validated['TransAmount'],
                'status' => 'completed',
                'result_desc' => 'Confirmed via C2B',
                'raw_response' => json_encode($request->all()),
            ]);
        } catch (QueryException $exception) {
            if (MpesaTransaction::where('transaction_id', $validated['TransID'])->exists()) {
                return response()->json(['ResultCode' => 0, 'ResultDesc' => 'Already processed']);
            }

            throw $exception;
        }

        if ($reservationId) {
            $reservation = Reservation::find($reservationId);
            if ($reservation && $reservation->status === 'PENDING_DEPOSIT' && ! $reservation->deposit_paid
                && Money::toMinor($validated['TransAmount']) >= Money::toMinor($reservation->deposit_amount)) {
                $reservation->update([
                    'deposit_paid' => true,
                    'deposit_paid_at' => now(),
                    'deposit_receipt_no' => $transaction->transaction_id,
                    'deposit_expires_at' => null,
                ]);
                $this->reservationService->transitionStatus($reservation, 'CONFIRMED');
                BookingDepositController::notifyFrontDesk(
                    $reservation,
                    'Reservation deposit received',
                    "Deposit received for reservation {$reservation->reservation_number}.",
                );
            }
        }

        return response()->json(['ResultCode' => 0, 'ResultDesc' => 'Success']);
    }

    /**
     * Register C2B URLs manually (Utility endpoint)
     */
    public function registerUrls()
    {
        abort_unless(auth()->user()?->hasPermission('settings.manage'), 403);

        try {
            $response = $this->mpesaService->registerC2BUrls(
                config('services.mpesa.c2b_validation_url'),
                config('services.mpesa.c2b_confirmation_url')
            );

            return response()->json($response);
        } catch (\Exception $e) {
            report($e);

            return response()->json(['error' => 'Unable to register the callback URLs.'], 500);
        }
    }

    private function canAccessTransaction(MpesaTransaction $transaction, User $user): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        $branchId = $transaction->invoice?->branch_id
            ?? $transaction->order?->branch_id
            ?? $transaction->reservation?->branch_id;

        return $branchId !== null && (int) $branchId === (int) $user->branch_id;
    }
}
