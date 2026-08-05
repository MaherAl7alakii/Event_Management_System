<?php


use App\Http\Controllers\PortfolioController;
use Illuminate\Support\Facades\Route;


Route::get('/service-providers/{serviceProvider}/portfolios',[PortfolioController::class, 'index']);
Route::get('/portfolios/{portfolio}',[PortfolioController::class, 'show']);



Route::middleware(['auth:api','check.banned',])->group(function () {
    Route::post('/portfolio',[PortfolioController::class, 'store']);
    Route::post('/portfolio/{portfolio}',[PortfolioController::class, 'update']);
    Route::delete('/portfolio/{portfolio}',[PortfolioController::class, 'destroy']);
});