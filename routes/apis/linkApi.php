<?php

use App\Http\Controllers\ServiceLinkController;
use Illuminate\Support\Facades\Route;



Route::middleware(['auth:sanctum'])->group(function () {


    Route::get('/services/{service}/links', [ServiceLinkController::class, 'index'])
        ->name('services.links.index');


    Route::post('/services/{service}/links', [ServiceLinkController::class, 'store'])
        ->name('services.links.store');


    Route::delete('/services/{service}/links/{linkedService}', [ServiceLinkController::class, 'destroy'])
        ->name('services.links.destroy');

});
