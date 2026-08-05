<?php

use App\Http\Controllers\NotificationController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:api','setLanguage'])->prefix('notifications')->group(function () {
    Route::get('/', [NotificationController::class, 'index']);
    Route::get('/{notification}', [NotificationController::class, 'show']);
    Route::post('/mark-all-read', [NotificationController::class, 'markAllRead']);
    Route::delete('/{notification}', [NotificationController::class, 'destroy']);
    Route::delete('/', [NotificationController::class, 'destroyAll']);
});
