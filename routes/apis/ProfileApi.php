<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:api','check.banned'])->group(function () {
    Route::get('profile', [ProfileController::class, 'show']);
    Route::post('profile', [ProfileController::class, 'store']);
    Route::post('profile/update', [ProfileController::class, 'update']);
});
