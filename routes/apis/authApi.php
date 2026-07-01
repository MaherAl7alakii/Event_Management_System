<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::post('/customer/register', [AuthController::class, 'register']);
Route::post('/service_provider/register', [AuthController::class, 'register']);

Route::post('/admin/login', [AuthController::class, 'login']);
Route::post('/customer/login', [AuthController::class, 'login']);
Route::post('/service_provider/login', [AuthController::class, 'login']);

Route::post('/customer/google/login', [AuthController::class, 'loginWithGoogle']);
Route::post('/service_provider/google/login', [AuthController::class, 'loginWithGoogle']);
Route::post('/admin/google/login', [AuthController::class, 'loginWithGoogle']);

Route::post('/refresh', [AuthController::class, 'refresh']);


Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/verify-reset-otp', [AuthController::class, 'verifyResetOtp']);
Route::post('/resend-reset-otp', [AuthController::class, 'resendResetOtp']);

Route::middleware('auth:api')->group(function () {

   Route::post('/logout', [AuthController::class, 'logout']);


   Route::post('/email/send-otp', [AuthController::class, 'sendOtp']);
   Route::post('/email/verify-otp', [AuthController::class, 'verifyOtp']);


   Route::post('/reset-password', [AuthController::class, 'resetPassword']);
});


