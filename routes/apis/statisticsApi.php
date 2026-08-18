<?php

use App\Http\Controllers\BookingController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\StatisticsController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:api'])->group(function () {
    Route::get('/provider/statistics', [StatisticsController::class, 'providerStatistics'])->middleware('role:service_provider');
});

Route::prefix('dashboard')->middleware('auth:api')->group(function () {
    Route::get('overview', [DashboardController::class, 'overview']);
    Route::get('bookings-chart', [DashboardController::class, 'bookingsChart']);
    Route::get('payments-chart', [DashboardController::class, 'paymentsChart']);
    Route::get('refunds-chart', [DashboardController::class, 'refundsChart']);
    Route::get('payouts-chart', [DashboardController::class, 'payoutsChart']);
    Route::get('providers-count', [DashboardController::class, 'providersCount']);
});
