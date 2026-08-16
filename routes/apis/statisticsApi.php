<?php

use App\Http\Controllers\BookingController;
use App\Http\Controllers\StatisticsController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:api'])->group(function () {
    Route::get('/provider/statistics', [StatisticsController::class, 'providerStatistics'])->middleware('role:service_provider');
});
