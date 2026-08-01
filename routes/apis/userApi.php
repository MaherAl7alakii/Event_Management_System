<?php

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:api','check.banned'])->prefix('users')->group(function () {
    Route::get('/customers',[UserController::class,'getCustomers']);
    Route::get('/providers',[UserController::class,'getServiceProviders']);
    Route::post('/{user}/ban', [UserController::class, 'ban']);
    Route::post('/{user}/unban', [UserController::class, 'unban']);
});
