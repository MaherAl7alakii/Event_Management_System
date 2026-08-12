<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FavoriteController;


Route::middleware(['auth:api', 'check.banned'])->group(function () {

    Route::post('favorites', [FavoriteController::class, 'store']);

    Route::delete('favorites', [FavoriteController::class, 'destroy']);

    Route::get('favorites', [FavoriteController::class, 'index']);
    
    Route::get('favorites/services', [FavoriteController::class, 'services']);

Route::get('favorites/providers', [FavoriteController::class, 'providers']);

});