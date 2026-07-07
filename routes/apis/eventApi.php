<?php


use App\Http\Controllers\EventController;
use App\Http\Controllers\EventTypeController;
use Illuminate\Support\Facades\Route;

Route::middleware(['setLanguage','auth:api','verified.email' ,'role:customer'])->group(function () {
    Route::get('events/types',[EventTypeController::class,'index']);
    Route::apiResource('events',EventController::class);
});
