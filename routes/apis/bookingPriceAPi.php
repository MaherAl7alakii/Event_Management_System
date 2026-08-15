<?php



use App\Http\Controllers\BookingPriceController;

use Illuminate\Support\Facades\Route;

Route::middleware('auth:api')->group(function () {

    Route::post('/bookings/{booking}/price-edit', [BookingPriceController::class, 'propose']);
    Route::get('/bookings/{booking}/price-proposals', [BookingPriceController::class, 'history']);
    Route::post('/price-proposals/{proposal}/respond', [BookingPriceController::class, 'respond']);


});


