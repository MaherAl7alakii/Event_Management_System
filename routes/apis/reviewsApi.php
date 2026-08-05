<?php



use App\Http\Controllers\ReviewController;
use Illuminate\Support\Facades\Route;

Route::get('/service-providers/{serviceProvider}/reviews',[ReviewController::class,'index']);


Route::middleware(['auth:api','check.banned'])->group(function(){
    Route::post('/reviews',[ReviewController::class,'store']);
    Route::put('/reviews/{review}',[ReviewController::class,'update']);
    Route::delete('/reviews/{review}',[ReviewController::class,'destroy']);

});