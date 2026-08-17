<?php


use App\Http\Controllers\BookingModificationController;
use App\Http\Controllers\BookingPriceController;

use Illuminate\Support\Facades\Route;

Route::middleware('auth:api')->group(function () {

    Route::post('/bookings/{booking}/price-edit', [BookingPriceController::class, 'propose']);
    Route::get('/bookings/{booking}/price-proposals', [BookingPriceController::class, 'history']);
    Route::post('/price-proposals/{proposal}/respond', [BookingPriceController::class, 'respond']);

    Route::post('/bookings/{booking}/modifications', [BookingModificationController::class, 'propose']);
    Route::get('/bookings/{booking}/modifications', [BookingModificationController::class, 'history']);
    Route::post('/provider/modifications/{modification}/respond', [BookingModificationController::class, 'respond'])
    ->middleware('role:service_provider');
    Route::post('/customer/modifications/{modification}/respond', [BookingModificationController::class, 'respond'])
    ->middleware('role:customer');

});


