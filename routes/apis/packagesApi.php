<?php

use App\Http\Controllers\PackageController;
use Illuminate\Support\Facades\Route;

Route::get(
    'service-providers/{provider}/packages',
    [PackageController::class,'index']
);



Route::get(
    'packages/{package}',
    [PackageController::class,'show']
);


Route::middleware(['auth:api','check.banned','provider.approved'])
->group(function(){

    Route::post(
        'packages',
        [PackageController::class,'store']
    );


    Route::POST(
        'packages/{package}',
        [PackageController::class,'update']
    );


    Route::delete(
        'packages/{package}',
        [PackageController::class,'destroy']
    );

});