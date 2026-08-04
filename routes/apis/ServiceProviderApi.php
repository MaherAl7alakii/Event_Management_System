<?php

use App\Http\Controllers\ServiceProviderController;
use App\Http\Controllers\ServiceProviderApprovalController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:api','check.banned'])->group(function () {
    Route::get('service-provider', [ServiceProviderController::class, 'show']);
    Route::post('service-provider', [ServiceProviderController::class, 'store'])->name('provider.store');
    Route::post('service-provider/update', [ServiceProviderController::class, 'update']);
    Route::post('/service-provider/resubmit',[ServiceProviderApprovalController::class,'resubmit']);
});


Route::middleware(['auth:api','role:admin'])->prefix('admin')->group(function(){
    Route::put('/service-providers/{serviceProvider}/approve',[ServiceProviderApprovalController::class,'approve']);
    Route::put('/service-providers/{serviceProvider}/reject',[ServiceProviderApprovalController::class,'reject']);
});


