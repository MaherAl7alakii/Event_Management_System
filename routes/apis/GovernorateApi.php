<?php

use App\Http\Controllers\GovernorateController;
use Illuminate\Support\Facades\Route;

Route::get('governorates', [GovernorateController::class, 'index']);
Route::get('governorate/{governorate}/cities', [GovernorateController::class, 'getGovernorateCities']);
