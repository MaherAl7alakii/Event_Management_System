<?php



use App\Http\Controllers\AdminRefundController;
use App\Http\Controllers\BookingCancellationController;
use App\Http\Controllers\BookingComplaintController;
use App\Http\Controllers\BookingPriceController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:api')->group(function () {



    Route::post('/bookings/{booking}/complaints', [BookingComplaintController::class, 'complaintBooking']);

    Route::middleware(['role:admin'])->group(function () {
        Route::get('/admin/complaints', [BookingComplaintController::class, 'index']);
        Route::get('/admin/complaints/{complaint}', [BookingComplaintController::class, 'show']);
        Route::post('/admin/complaints/{complaint}/resolve', [BookingComplaintController::class, 'resolve']);
        Route::post('/admin/refunds', [AdminRefundController::class, 'refund']);
    });

});




