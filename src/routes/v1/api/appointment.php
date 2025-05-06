<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Appointment\AppointmentController;

Route::controller(AppointmentController::class)
    //->middleware('auth:sanctum')
    ->prefix('v1/appointments')
    ->group(function () {
        Route::get('', 'index');
        Route::get('/{id}', 'getById');
        Route::post('/', 'create');
        Route::put('/{id}', 'update');
        Route::delete('/{id}', 'delete');
});
