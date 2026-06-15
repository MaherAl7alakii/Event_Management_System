<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::post('/customer/register', [AuthController::class, 'register']);
Route::post('/service_provider/register', [AuthController::class, 'register']);

Route::post('/admin/login', [AuthController::class, 'login']);
Route::post('/customer/login', [AuthController::class, 'login']);
Route::post('/service_provider/login', [AuthController::class, 'login']);

Route::post('/refresh', [AuthController::class, 'refresh']);




Route::middleware('auth:api')->group(function () {
  
    Route::post('/email/verify/send-otp',[AuthController::class, 'sendEmailVerificationOtp'])->name('email.verify.send-otp');
    Route::post('/email/verify',[AuthController::class, 'verifyEmail'])->name('email.verify');


Route::post('/forgot-password/send-otp',[AuthController::class, 'sendForgotPasswordOtp'])->name('password.forgot.send-otp');
Route::post('/forgot-password/verify-otp',[AuthController::class, 'verifyForgotPasswordOtp'])->name('password.forgot.verify-otp');
Route::post('/reset-password',[AuthController::class, 'resetPassword'])->name('password.reset');

Route::post('/otp/resend',[AuthController::class, 'resendOtp'])->name('otp.resend');



    Route::post('/logout', [AuthController::class, 'logout']);
});