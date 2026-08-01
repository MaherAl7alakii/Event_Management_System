<?php

use App\Http\Controllers\BookingController;
use App\Http\Controllers\BookingStatusController;
use Illuminate\Support\Facades\Route;

Route::prefix('bookings')->middleware(['auth:api','verified.email','setLanguage','provider.approved','check.banned'])->group(function () {

    Route::get('/', [BookingController::class, 'index'])->middleware('permission:view_bookings');

    Route::get('events/{event}', [BookingController::class, 'getEventBookings'])->middleware('permission:view_event_bookings');

    Route::post('/', [BookingController::class, 'store'])->middleware('permission:create_booking');

    Route::get('/service/{service}/estimate-price', [BookingController::class, 'estimatePrice'])->middleware('permission:estimate_booking_price');
    Route::get('/service/{service}/check-availability', [BookingController::class, 'checkAvailability']);

    Route::prefix('{booking}')->group(function () {
        Route::get('/', [BookingController::class, 'show'])->middleware('permission:view_bookings');
        Route::put('/', [BookingController::class, 'update'])->middleware('permission:update_booking');
        Route::post('/accept', [BookingStatusController::class, 'accept'])->middleware('permission:accept_or_reject_booking');
        Route::post('/reject', [BookingStatusController::class, 'reject'])->middleware('permission:accept_or_reject_booking');

    });

});


