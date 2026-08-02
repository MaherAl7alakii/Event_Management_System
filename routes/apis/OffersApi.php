<?php

use App\Http\Controllers\OfferController;
use Illuminate\Support\Facades\Route;


 Route::get('/offers/{service}', [OfferController::class, 'show']);

Route::middleware(['auth:api','provider.approved'])
->group(function () {

    Route::post('/offers/{service}', [OfferController::class, 'store']);

    Route::put('/offers/{service}', [OfferController::class, 'update']);

    Route::delete('/offers/{service}', [OfferController::class, 'destroy']);

});