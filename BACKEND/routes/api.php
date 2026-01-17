<?php

use App\Http\Controllers\{
	MissionController,
	UserController,
	VehicleController
};
use Illuminate\Support\Facades\Route;

Route::get('/', fn() => _200(env("APP_NAME") . " API is running"));

// Route::post('users/{user}', [UserController::class, 'updateImage']);

Route::apiResources([
	'vehicles' => VehicleController::class,
	'missions' => MissionController::class,
	'users' => UserController::class
]);
