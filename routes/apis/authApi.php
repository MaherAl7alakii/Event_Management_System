<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::post('/customer/register', [AuthController::class, 'register']);
Route::post('/service_provider/register', [AuthController::class, 'register']);

Route::post('/admin/login', [AuthController::class, 'login']);
Route::post('/customer/login', [AuthController::class, 'login']);
Route::post('/service_provider/login', [AuthController::class, 'login']);

Route::post('/refresh', [AuthController::class, 'refresh']);



Route::post('/send-otp', [AuthController::class, 'sendOtp']);
Route::post('/verify-otp', [AuthController::class, 'verifyOtp']);


Route::post('/forgot-password',[AuthController::class, 'forgotPassword']);
Route::post('/reset-password',[AuthController::class, 'resetPassword']);
Route::post('/send-Forgot-PasswordOtp', [AuthController::class, 'sendForgotPasswordOtp']);


Route::middleware('auth:api')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
});

