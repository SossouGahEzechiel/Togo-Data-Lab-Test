<?php

use App\Http\Controllers\VehicleController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn() => _200(env("APP_NAME"). " API is running"));

Route::apiResource('vehicles', VehicleController::class);
