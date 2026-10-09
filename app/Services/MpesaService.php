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
        $this->env = config('services.mpesa.env', 'sandbox');
        $this->baseUrl = $this->env === 'live'
            ? 'https://api.safaricom.co.ke'
            : 'https://sandbox.safaricom.co.ke';
        $this->consumerKey = config('services.mpesa.consumer_key');
        $this->consumerSecret = config('services.mpesa.consumer_secret');
        $this->shortcode = config('services.mpesa.shortcode');
        $this->passkey = config('services.mpesa.passkey');
        $this->b2cShortcode = config('services.mpesa.b2c_shortcode');
        $this->initiatorName = config('services.mpesa.initiator_name');
        $this->initiatorPassword = config('services.mpesa.initiator_password');
    }

    /**
     * Generate an access token from Safaricom.
     */
    public function getAccessToken()
    {
        $url = $this->baseUrl.'/oauth/v1/generate?grant_type=client_credentials';
        $credentials = base64_encode($this->consumerKey.':'.$this->consumerSecret);

        $response = Http::connectTimeout(5)->timeout(15)->withHeaders([
            'Authorization' => 'Basic '.$credentials,
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
        $url = $this->baseUrl.'/mpesa/stkpush/v1/processrequest';

        // Format phone number to 254XXXXXXXXX
        $formattedPhone = $this->formatPhoneNumber($phoneNumber);

        $timestamp = date('YmdHis');
        $password = base64_encode($this->shortcode.$this->passkey.$timestamp);

        $payload = [
            'BusinessShortCode' => $this->shortcode,
            'Password' => $password,
            'Timestamp' => $timestamp,
            'TransactionType' => 'CustomerPayBillOnline',
            'Amount' => $amount,
            'PartyA' => $formattedPhone,
            'PartyB' => $this->shortcode,
            'PhoneNumber' => $formattedPhone,
            'CallBackURL' => config('services.mpesa.callback_url'),
            'AccountReference' => $accountReference,
            'TransactionDesc' => $transactionDesc,
        ];

        $response = Http::connectTimeout(5)->timeout(20)->withToken($this->getAccessToken())->post($url, $payload);

        return $response->json();
    }

    /**
     * Register C2B URLs (Validation and Confirmation URLs)
     */
    public function registerC2BUrls($validationUrl, $confirmationUrl)
    {
        $url = $this->baseUrl.'/mpesa/c2b/v1/registerurl';

        $payload = [
            'ShortCode' => $this->shortcode,
            'ResponseType' => 'Completed',
            'ConfirmationURL' => $confirmationUrl,
            'ValidationURL' => $validationUrl,
        ];

        $response = Http::connectTimeout(5)->timeout(20)->withToken($this->getAccessToken())->post($url, $payload);

        return $response->json();
    }

    public function queryStkPush(string $checkoutRequestId): array
    {
        $timestamp = now()->format('YmdHis');
        $payload = [
            'BusinessShortCode' => $this->shortcode,
            'Password' => base64_encode($this->shortcode.$this->passkey.$timestamp),
            'Timestamp' => $timestamp,
            'CheckoutRequestID' => $checkoutRequestId,
        ];

        return Http::connectTimeout(5)
            ->timeout(20)
            ->withToken($this->getAccessToken())
            ->post($this->baseUrl.'/mpesa/stkquery/v1/query', $payload)
            ->throw()
            ->json();
    }

    /**
     * Format phone number to 254 format
     */
    private function formatPhoneNumber($phoneNumber)
    {
        $phoneNumber = preg_replace('/[^0-9]/', '', $phoneNumber);

        if (str_starts_with($phoneNumber, '0')) {
            $phoneNumber = '254'.substr($phoneNumber, 1);
        } elseif (str_starts_with($phoneNumber, '+254')) {
            $phoneNumber = substr($phoneNumber, 1);
        }

        return $phoneNumber;
    }
}
