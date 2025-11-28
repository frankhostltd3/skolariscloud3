<?php

use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['web']], function () {
    Route::post('/fees/pay', [\Skolaris\FeesPay\Http\Controllers\PaymentController::class, 'initiate']);
    Route::get('/fees/callback', [\Skolaris\FeesPay\Http\Controllers\PaymentController::class, 'callback']);
});
