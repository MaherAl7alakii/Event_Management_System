<?php

use App\Http\Controllers\GalleryController;
use Illuminate\Support\Facades\Route;


Route::get(
    '/service-providers/{serviceProvider}/gallery',
    [GalleryController::class, 'index']
);


Route::get(
    '/gallery/{gallery}',
    [GalleryController::class, 'show']
);


Route::get(
    '/service-providers/{serviceProvider}/gallery/categories',
    [GalleryController::class, 'categories']
);

Route::middleware([
    'auth:api',
    'provider.approved'
])->group(function () {


    Route::post(
        '/gallery',
        [GalleryController::class, 'store']
    );


    Route::put(
        '/gallery/{gallery}',
        [GalleryController::class, 'update']
    );


    Route::delete(
        '/gallery/{gallery}',
        [GalleryController::class, 'destroy']
    );

});