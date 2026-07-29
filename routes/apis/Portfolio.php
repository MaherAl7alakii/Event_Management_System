<?php


use App\Http\Controllers\PortfolioController;
use Illuminate\Support\Facades\Route;


// Customer + Service Provider (عرض)
Route::get(
    '/service-providers/{serviceProvider}/portfolios',
    [PortfolioController::class, 'index']
);


// عرض Portfolio واحد
Route::get(
    '/portfolios/{portfolio}',
    [PortfolioController::class, 'show']
);




/*
|--------------------------------------------------------------------------
| Service Provider Only
|--------------------------------------------------------------------------
*/

Route::middleware(['auth:api'])->group(function () {

    Route::post(
        '/portfolio',
        [PortfolioController::class, 'store']
    );


    Route::post(
        '/portfolio/{portfolio}',
        [PortfolioController::class, 'update']
    );


    Route::delete(
        '/portfolio/{portfolio}',
        [PortfolioController::class, 'destroy']
    );

});