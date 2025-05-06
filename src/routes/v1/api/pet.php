<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Pet\PetController;

Route::controller(PetController::class)
    //->middleware('auth:sanctum')
    ->prefix('v1/pets')
    ->group(function () {
        Route::get('', 'index');
        Route::get('/{id}', 'getById');
        Route::post('/', 'create');
        Route::put('/{id}', 'update');
        Route::delete('/{id}', 'delete');
});
