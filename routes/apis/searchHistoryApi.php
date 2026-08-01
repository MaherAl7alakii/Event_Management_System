<?php

use App\Http\Controllers\SearchHistoryController;
use Illuminate\Support\Facades\Route;


Route::middleware(['setLanguage','auth:api','verified.email','provider.approved','check.banned'])->group(function () {

    Route::prefix('search-history')->group(function () {
        Route::get('/', [SearchHistoryController::class, 'index']);
        Route::delete('/{id}', [SearchHistoryController::class, 'destroy']);
        Route::delete('/', [SearchHistoryController::class, 'clearAll']);
    });
});
