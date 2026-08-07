<?php


use App\Http\Controllers\ConversationController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\TypingController;
use Illuminate\Support\Facades\Route;


Route::middleware('auth:api')->group(function () {

    Route::get('/conversations', [ConversationController::class, 'index']);
    Route::post('/conversations', [ConversationController::class, 'findOrCreate']);
//    Route::get('/conversations/{conversation}', [ConversationController::class, 'show']);

    Route::get('/conversations/{conversation}/messages', [MessageController::class, 'index']);
    Route::post('/conversations/{conversation}/messages', [MessageController::class, 'store']);
    Route::put('/conversations/{conversation}/messages/{message}', [MessageController::class, 'update']);
    Route::delete('/conversations/{conversation}/messages/{message}', [MessageController::class, 'destroy']);


    Route::post('/conversations/{conversation}/read', [MessageController::class, 'markAsRead']);

    Route::post('/conversations/{conversation}/typing', TypingController::class);
});
