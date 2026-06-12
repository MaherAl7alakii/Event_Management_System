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
    Route::post('/logout', [AuthController::class, 'logout']);
});

