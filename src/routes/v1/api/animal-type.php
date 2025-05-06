<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AnimalType\AnimalTypeController;

Route::controller(AnimalTypeController::class)
    //->middleware('auth:sanctum')
    ->prefix('v1/animal-types')
    ->group(function () {
        Route::get('', 'index');
        Route::get('/{id}', 'getById');
});
