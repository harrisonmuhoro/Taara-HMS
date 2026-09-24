<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MpesaController;

Route::prefix('mpesa')->group(function () {
    // Initiate STK Push
    Route::post('/stkpush/initiate', [MpesaController::class, 'initiateStkPush']);

    // STK Push Callback (Webhook from Safaricom)
    Route::post('/callback', [MpesaController::class, 'stkCallback']);

    // C2B Registration (Call this once to setup C2B)
    Route::get('/c2b/register', [MpesaController::class, 'registerUrls']);
});

// C2B Webhooks — paths must NOT contain the word 'mpesa' (Safaricom restriction)
Route::post('/c2b/validate', [MpesaController::class, 'c2bValidation']);
Route::post('/c2b/confirm', [MpesaController::class, 'c2bConfirmation']);
