<?php

use App\Http\Controllers\MpesaController;
use Illuminate\Support\Facades\Route;

Route::prefix('mpesa')->group(function () {
    Route::middleware(['web', 'auth', 'verified'])->group(function () {
        // F-01: only roles with payments.collect may trigger an STK prompt
        // F-10: 5 pushes/min per user, 3/10-min per guest phone
        Route::post('/stkpush/initiate', [MpesaController::class, 'initiateStkPush'])
            ->middleware(['can:payments.collect', 'throttle:mpesa-stk']);

        Route::get('/status/{checkoutRequestId}', [MpesaController::class, 'queryStatus'])
            ->middleware('throttle:mpesa-status');

        Route::get('/c2b/register', [MpesaController::class, 'registerUrls']);
    });

    // STK Push Callback (Webhook from Safaricom)
    Route::post('/callback', [MpesaController::class, 'stkCallback'])->middleware('mpesa.webhook');

});

// C2B Webhooks — paths must NOT contain the word 'mpesa' (Safaricom restriction)
Route::post('/c2b/validate', [MpesaController::class, 'c2bValidation'])->middleware('mpesa.webhook');
Route::post('/c2b/confirm', [MpesaController::class, 'c2bConfirmation'])->middleware('mpesa.webhook');
