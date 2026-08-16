<?php

use App\Http\Controllers\StripeWebhookController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\PaymentController;



Route::middleware(['auth:api'])->group(function () {


    Route::get('/customer/payments', [PaymentController::class, 'payments'])->middleware('role:customer');
    Route::get('/customer/payments/refunds', [PaymentController::class, 'refunds'])->middleware('role:customer');

    Route::prefix('provider')->middleware(['role:service_provider'])->group(function () {
        Route::get('/payouts', [PaymentController::class, 'payouts']);

        Route::get('/payouts/summary', [PaymentController::class, 'payoutsSummary']);
    });

    Route::prefix('admin')->middleware(['role:admin'])->group(function () {
        Route::get('/payments', [PaymentController::class, 'adminPayments']);

        Route::get('/payouts', [PaymentController::class, 'adminPayouts']);
        Route::get('/payments/refunds', [PaymentController::class, 'adminRefunds']);
    });

});




Route::post('/webhook/stripe', [StripeWebhookController::class, 'handle']);
