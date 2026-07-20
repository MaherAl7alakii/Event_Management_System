<?php


use App\Http\Controllers\EventController;
use App\Http\Controllers\EventTypeController;
use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Route;

Route::middleware(['setLanguage','auth:api','verified.email' ,'role:customer'])->group(function () {
    Route::get('events/types',[EventTypeController::class,'index']);
    Route::apiResource('events',EventController::class);
    Route::post('/events/{event}/submit', [PaymentController::class, 'submit']);
    Route::post('/events/{event}/payments/deposit/intent', [PaymentController::class, 'createDepositIntent']);
    Route::post('/events/{event}/payments/final-balance/intent', [PaymentController::class, 'createFinalBalanceIntent']);
});
