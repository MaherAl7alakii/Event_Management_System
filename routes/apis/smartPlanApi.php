<?php

use App\Http\Controllers\EventController;
use App\Http\Controllers\SmartBudgetController;
use Illuminate\Support\Facades\Route;

Route::prefix('smart-budget')->group(function () {
    Route::post('/generate', [SmartBudgetController::class, 'generate']);

    Route::post('/recalculate', [SmartBudgetController::class, 'recalculate']);

});
Route::post('/events/with-bookings', [EventController::class, 'storeEventWithBookings'])->middleware('auth:api')
    ->name('events.storeWithBookings');
