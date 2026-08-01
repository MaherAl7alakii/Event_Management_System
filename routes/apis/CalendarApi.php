<?php

use App\Enums\TimeOffReason;
use App\Http\Controllers\CalendarController;
use Illuminate\Support\Facades\Route;



Route::prefix('provider')->middleware(['auth:api','verified.email','setLanguage','provider.approved'])->group(function () {

   Route::get('/working-hours',[CalendarController::class,'getWorkingHours']);
   Route::Put('/working-hours',[CalendarController::class,'updateWorkingHours'])->middleware('permission:update_working_hours');

    Route::get('/calendar', [CalendarController::class, 'getDayView'])->middleware('permission:calendar_view');
    Route::get('/calendar/month', [CalendarController::class, 'getMonthOverview'])->middleware('permission:calendar_view');


    Route::post('/time-offs', [CalendarController::class, 'storeTimeOff'])->middleware('permission:add_time_off');
    Route::put('/time-offs/{timeOff}', [CalendarController::class, 'updateTimeOff'])->middleware('permission:update_time_off');
    Route::delete('/time-offs/{timeOff}', [CalendarController::class, 'destroyTimeOff'])->middleware('permission:delete_time_off');

    Route::get('/time-offs/reasons', function () {
        return response()->json([
            'status'  => 200,
            'message' => "Get time off reasons success",
            'data'    => TimeOffReason::options(),

        ]);
    });

    Route::get('/time-offs/{timeOff}', [CalendarController::class, 'showTimeOff']);
});
