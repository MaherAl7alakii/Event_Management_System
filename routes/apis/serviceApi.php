<?php

use App\Enums\PricingType;
use App\Http\Controllers\ServiceController;
use Illuminate\Support\Facades\Route;

Route::middleware('setLanguage')->group(function () {

    Route::get('/services', [ServiceController::class, 'index'])->name('services.index');

    Route::get('/service_provider/services', [ServiceController::class, 'index'])
        ->middleware(['auth:api', 'verified.email','provider.approved','check.banned'])
        ->name('services.index');

    Route::get('/services/pricing-types', function() {
        return response()->json([
            'status' => 200,
            'data' => PricingType::options(),
            'message' => "Get pricing types success"
        ]);
    });


    Route::get('/services/{service}', [ServiceController::class, 'show']);

    Route::get('services/search/suggestions', [ServiceController::class, 'searchSuggestions']);

    Route::middleware(['auth:api', 'verified.email','provider.approved','check.banned'])->group(function () {
        Route::post('/services', [ServiceController::class, 'store'])->middleware('permission:create_service');;
        Route::put('/services/{service}', [ServiceController::class, 'update'])->middleware('permission:update_service');;
        Route::delete('/services/{service}', [ServiceController::class, 'destroy']);
    });




Route::get('/service-providers/{provider}/categories/{category}/services',[ServiceController::class, 'providerCategoryServices']);
Route::get('/service-providers/{provider}/offers',[ServiceController::class, 'providerOfferServices']);

});
