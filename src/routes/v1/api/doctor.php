<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Doctor\DoctorController;

Route::controller(DoctorController::class)
    ->middleware('auth:sanctum')
    ->prefix('v1/doctors')
    ->group(function () {
        Route::get('', 'index');
    });
