<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Appointment\AppointmentController;

Route::controller(AppointmentController::class)
    ->middleware('auth:sanctum')
    ->prefix('v1/appointments')
    ->group(function () {
        Route::get('', 'index');//->middleware('permission:view all appointments');
        Route::get('/{id}', 'getById');//->middleware('permission:view all appointments');
        Route::post('/', 'create');//->middleware('permission:create appointments');
        Route::put('/{id}', 'update');//->middleware('permission:edit appointments');
        Route::delete('/{id}', 'delete');//->middleware('permission:delete appointments');
});
