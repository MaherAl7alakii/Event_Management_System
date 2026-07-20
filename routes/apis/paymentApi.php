<?php

use App\Http\Controllers\StripeWebhookController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\PaymentController;




Route::post('/webhook/stripe', [StripeWebhookController::class, 'handle']);
