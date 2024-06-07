<?php

use App\Http\Controllers\PaymentController;
use App\Http\Middleware\ValidatePayFastIP;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::post('/payment/initialize', [PaymentController::class, 'initialize'])->name('payment.initialize');
    Route::post('/payment/show', [PaymentController::class, 'show'])->name('payment.show');
});

Route::post('/payment/notify', [PaymentController::class, 'handleNotification'])->name('payment.notify')->middleware(ValidatePayFastIP::class);