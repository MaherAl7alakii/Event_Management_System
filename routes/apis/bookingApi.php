<?php

use App\Http\Controllers\BookingController;
use App\Http\Controllers\BookingStatusController;
use Illuminate\Support\Facades\Route;

Route::prefix('bookings')->middleware(['auth:api','verified.email','setLanguage','provider.approved'])->group(function () {

    Route::get('/', [BookingController::class, 'index']);

    Route::post('/', [BookingController::class, 'store']);

    Route::get('/service/{service}/estimate-price', [BookingController::class, 'estimatePrice']);

    Route::prefix('{booking}')->group(function () {
        Route::get('/', [BookingController::class, 'show']);
        Route::put('/', [BookingController::class, 'update']);
        Route::post('/accept', [BookingStatusController::class, 'accept']);
        Route::post('/reject', [BookingStatusController::class, 'reject']);

    });

});


