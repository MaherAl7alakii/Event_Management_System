<?php

use App\Http\Controllers\SmartBudgetController;
use Illuminate\Support\Facades\Route;

Route::prefix('smart-budget')->group(function () {
    Route::post('/generate', [SmartBudgetController::class, 'generate']);

    Route::post('/recalculate', [SmartBudgetController::class, 'recalculate']);
});
