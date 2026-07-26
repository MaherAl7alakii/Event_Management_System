<?php

use App\Enums\TimeOffReason;
use App\Http\Controllers\CalendarController;
use Illuminate\Support\Facades\Route;



Route::prefix('provider')->middleware(['auth:api','verified.email','setLanguage','provider.approved'])->group(function () {

   Route::get('/working-hours',[CalendarController::class,'getWorkingHours']);
   Route::Put('/working-hours',[CalendarController::class,'updateWorkingHours']);

    Route::get('/calendar', [CalendarController::class, 'getDayView']);
    Route::get('/calendar/month', [CalendarController::class, 'getMonthOverview']);

    Route::get('/time-offs/{timeOff}', [CalendarController::class, 'showTimeOff']);
    Route::post('/time-offs', [CalendarController::class, 'storeTimeOff']);
    Route::put('/time-offs/{timeOff}', [CalendarController::class, 'updateTimeOff']);
    Route::delete('/time-offs/{timeOff}', [CalendarController::class, 'destroyTimeOff']);

    Route::get('/time-offs/reasons', function () {
        return response()->json([
            'status'  => 200,
            'message' => "Get time off reasons success",
            'data'    => TimeOffReason::options(),

        ]);
    });

});
