<?php

use App\Http\Controllers\CategoryController;
use Illuminate\Support\Facades\Route;

Route::middleware('setLanguage')->group(function () {

    Route::get('categories', [CategoryController::class, 'index']);
    Route::middleware(['auth:api' ,'role:admin'])->group(function () {
        Route::get('categories/{category}', [CategoryController::class, 'show']);
        Route::post('categories', [CategoryController::class, 'store']);
        Route::put('categories/{category}', [CategoryController::class, 'update']);
    });

});

