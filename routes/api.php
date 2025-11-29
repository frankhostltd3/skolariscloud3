<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Tenant\Api\PaymentApiController;
use App\Http\Controllers\Tenant\Api\PaymentWebhookController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group.
|
*/

// =============================================
// PAYMENT WEBHOOKS (No Authentication Required)
// =============================================
// These routes receive callbacks from payment providers
Route::prefix('payments/webhooks')->name('payment.webhook.')->group(function () {
    Route::post('/{provider}', [PaymentWebhookController::class, 'handle'])->name('handle');
    
    // Provider-specific routes for convenience
    Route::post('/mtn-momo', [PaymentWebhookController::class, 'mtnMomo'])->name('mtn-momo');
    Route::post('/mpesa', [PaymentWebhookController::class, 'mpesa'])->name('mpesa');
    Route::post('/airtel-money', [PaymentWebhookController::class, 'airtelMoney'])->name('airtel-money');
    Route::post('/flutterwave', [PaymentWebhookController::class, 'flutterwave'])->name('flutterwave');
    Route::post('/paystack', [PaymentWebhookController::class, 'paystack'])->name('paystack');
    Route::post('/orange-money', [PaymentWebhookController::class, 'orangeMoney'])->name('orange-money');
    Route::post('/yo-payments', [PaymentWebhookController::class, 'yoPayments'])->name('yo-payments');
    Route::post('/dpo', [PaymentWebhookController::class, 'dpo'])->name('dpo');
});

// =============================================
// PAYMENT API (Authentication Required)
// =============================================
Route::prefix('payments')->name('api.payments.')->middleware(['auth:sanctum'])->group(function () {
    // Get available gateways
    Route::get('/gateways', [PaymentApiController::class, 'gateways'])->name('gateways');
    
    // Initiate payment
    Route::post('/initiate', [PaymentApiController::class, 'initiate'])->name('initiate');
    
    // Check payment status
    Route::get('/status/{transactionId}', [PaymentApiController::class, 'status'])->name('status');
    
    // Payment history
    Route::get('/history', [PaymentApiController::class, 'history'])->name('history');
    
    // Statistics
    Route::get('/stats', [PaymentApiController::class, 'stats'])->name('stats');
    
    // Cancel payment
    Route::post('/{transactionId}/cancel', [PaymentApiController::class, 'cancel'])->name('cancel');
    
    // Retry failed payment
    Route::post('/{transactionId}/retry', [PaymentApiController::class, 'retry'])->name('retry');
});

// =============================================
// PUBLIC PAYMENT STATUS CHECK (No Auth)
// =============================================
Route::get('/payments/check/{transactionId}', [PaymentApiController::class, 'status'])
    ->name('api.payments.check');
