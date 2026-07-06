<?php


use App\Http\Controllers\EventTypeController;
use Illuminate\Support\Facades\Route;

Route::middleware('setLanguage')->group(function () {
    Route::get('events/types',[EventTypeController::class,'index']);
});
