<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\MpesaService;
use App\Models\MpesaTransaction;
use Illuminate\Support\Facades\Log;

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
        $request->validate([
            'phone' => 'required|string',
            'amount' => 'required|numeric|min:1'
        ]);

        try {
            $response = $this->mpesaService->stkPush(
                $request->phone, 
                $request->amount,
                'Taara Hotel',
                'Room Booking Payment'
            );

            if (isset($response['ResponseCode']) && $response['ResponseCode'] == '0') {
                MpesaTransaction::create([
                    'transaction_type' => 'STK_PUSH',
                    'merchant_request_id' => $response['MerchantRequestID'],
                    'checkout_request_id' => $response['CheckoutRequestID'],
                    'phone_number' => $request->phone,
                    'amount' => $request->amount,
                    'status' => 'pending',
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'STK Push initiated successfully. Please check your phone.',
                    'data' => $response
                ]);
            }

            return response()->json(['success' => false, 'message' => 'Failed to initiate STK push', 'data' => $response], 400);

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

        $resultCode = $callbackData['ResultCode'];
        $resultDesc = $callbackData['ResultDesc'];
        $checkoutRequestId = $callbackData['CheckoutRequestID'];

        $transaction = MpesaTransaction::where('checkout_request_id', $checkoutRequestId)->first();
        if (!$transaction) {
            Log::warning('M-Pesa STK Callback received for unknown transaction: ' . $checkoutRequestId);
            return response()->json(['success' => true]); // Still return success to Safaricom
        }

        if ($resultCode == 0) {
            // Payment successful
            $callbackItems = $callbackData['CallbackMetadata']['Item'];
            $mpesaReceiptNumber = '';
            
            foreach ($callbackItems as $item) {
                if ($item['Name'] == 'MpesaReceiptNumber') {
                    $mpesaReceiptNumber = $item['Value'];
                }
            }

            $transaction->update([
                'status' => 'completed',
                'transaction_id' => $mpesaReceiptNumber,
                'result_desc' => $resultDesc,
                'raw_response' => json_encode($request->all())
            ]);
            
            // Here you can trigger other events, like marking a booking as paid

        } else {
            // Payment failed or cancelled
            $transaction->update([
                'status' => 'failed',
                'result_desc' => $resultDesc,
                'raw_response' => json_encode($request->all())
            ]);
        }

        return response()->json(['success' => true]);
    }

    /**
     * Handle C2B Validation
     */
    public function c2bValidation(Request $request)
    {
        Log::info('C2B Validation: ', $request->all());
        
        // Accept all transactions in this example
        return response()->json([
            'ResultCode' => 0,
            'ResultDesc' => 'Accepted'
        ]);
    }

    /**
     * Handle C2B Confirmation
     */
    public function c2bConfirmation(Request $request)
    {
        Log::info('C2B Confirmation: ', $request->all());

        MpesaTransaction::create([
            'transaction_type' => 'C2B',
            'transaction_id' => $request->input('TransID'),
            'phone_number' => $request->input('MSISDN'),
            'amount' => $request->input('TransAmount'),
            'status' => 'completed',
            'result_desc' => 'Confirmed via C2B',
            'raw_response' => json_encode($request->all())
        ]);

        return response()->json([
            'ResultCode' => 0,
            'ResultDesc' => 'Success'
        ]);
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
