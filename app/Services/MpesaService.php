<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MpesaService
{
    protected $env;
    protected $baseUrl;
    protected $consumerKey;
    protected $consumerSecret;
    protected $shortcode;
    protected $passkey;
    protected $b2cShortcode;
    protected $initiatorName;
    protected $initiatorPassword;

    public function __construct()
    {
        $this->env = config('services.mpesa.env', env('MPESA_ENV', 'sandbox'));
        $this->baseUrl = $this->env === 'live' 
            ? 'https://api.safaricom.co.ke' 
            : 'https://sandbox.safaricom.co.ke';
        $this->consumerKey = env('MPESA_CONSUMER_KEY');
        $this->consumerSecret = env('MPESA_CONSUMER_SECRET');
        $this->shortcode = env('MPESA_SHORTCODE');
        $this->passkey = env('MPESA_PASSKEY');
        $this->b2cShortcode = env('MPESA_B2C_SHORTCODE');
        $this->initiatorName = env('MPESA_INITIATOR_NAME');
        $this->initiatorPassword = env('MPESA_INITIATOR_PASSWORD');
    }

    /**
     * Generate an access token from Safaricom.
     */
    public function getAccessToken()
    {
        $url = $this->baseUrl . '/oauth/v1/generate?grant_type=client_credentials';
        $credentials = base64_encode($this->consumerKey . ':' . $this->consumerSecret);

        $response = Http::withHeaders([
            'Authorization' => 'Basic ' . $credentials
        ])->get($url);

        if ($response->successful()) {
            return $response->json('access_token');
        }

        Log::error('M-Pesa Access Token Error: ', $response->json());
        throw new \Exception('Failed to generate M-Pesa Access Token.');
    }

    /**
     * Initiate an STK Push to a customer.
     */
    public function stkPush($phoneNumber, $amount, $accountReference = 'Hotel Booking', $transactionDesc = 'Payment for booking')
    {
        $url = $this->baseUrl . '/mpesa/stkpush/v1/processrequest';
        
        // Format phone number to 254XXXXXXXXX
        $formattedPhone = $this->formatPhoneNumber($phoneNumber);
        
        $timestamp = date('YmdHis');
        $password = base64_encode($this->shortcode . $this->passkey . $timestamp);
        
        $payload = [
            'BusinessShortCode' => $this->shortcode,
            'Password' => $password,
            'Timestamp' => $timestamp,
            'TransactionType' => 'CustomerPayBillOnline',
            'Amount' => $amount,
            'PartyA' => $formattedPhone,
            'PartyB' => $this->shortcode,
            'PhoneNumber' => $formattedPhone,
            'CallBackURL' => env('MPESA_CALLBACK_URL'),
            'AccountReference' => $accountReference,
            'TransactionDesc' => $transactionDesc
        ];

        $response = Http::withToken($this->getAccessToken())->post($url, $payload);
        
        return $response->json();
    }

    /**
     * Register C2B URLs (Validation and Confirmation URLs)
     */
    public function registerC2BUrls($validationUrl, $confirmationUrl)
    {
        $url = $this->baseUrl . '/mpesa/c2b/v1/registerurl';
        
        $payload = [
            'ShortCode' => $this->shortcode,
            'ResponseType' => 'Completed',
            'ConfirmationURL' => $confirmationUrl,
            'ValidationURL' => $validationUrl
        ];

        $response = Http::withToken($this->getAccessToken())->post($url, $payload);
        
        return $response->json();
    }

    /**
     * Format phone number to 254 format
     */
    private function formatPhoneNumber($phoneNumber)
    {
        $phoneNumber = preg_replace('/[^0-9]/', '', $phoneNumber);
        
        if (str_starts_with($phoneNumber, '0')) {
            $phoneNumber = '254' . substr($phoneNumber, 1);
        } elseif (str_starts_with($phoneNumber, '+254')) {
            $phoneNumber = substr($phoneNumber, 1);
        }

        return $phoneNumber;
    }
}
