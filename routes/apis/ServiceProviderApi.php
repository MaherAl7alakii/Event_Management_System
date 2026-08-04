<?php

use App\Http\Controllers\ServiceProviderController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:api','check.banned'])->group(function () {
    Route::get('service-provider', [ServiceProviderController::class, 'show']);
    Route::post('service-provider', [ServiceProviderController::class, 'store'])->name('provider.store');
    Route::post('service-provider/update', [ServiceProviderController::class, 'update']);
});
