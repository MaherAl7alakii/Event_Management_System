<?php

use App\Http\Controllers\CityController;
use Illuminate\Support\Facades\Route;



Route::middleware('setLanguage')->group(function () {
    Route::get('cities', [CityController::class, 'index']);
});


