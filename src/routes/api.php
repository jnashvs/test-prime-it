<?php
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

//Route::prefix('v1/api/test')->group(function () {
//    dd(Auth::user());
//});

require __DIR__ . '/v1/api/animal-type.php';
require __DIR__ . '/v1/api/pet.php';
require __DIR__ . '/v1/api/appointment.php';
