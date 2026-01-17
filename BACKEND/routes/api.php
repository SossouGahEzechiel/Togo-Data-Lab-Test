<?php

use App\Http\Controllers\{
    AuthController,
    MissionController,
	UserController,
	VehicleController
};
use Illuminate\Support\Facades\Route;

Route::get('/', fn() => _200(env("APP_NAME") . " API is running"));

Route::controller(AuthController::class)->prefix('auth')->group(function() {
	Route::post('login', 'login');
	Route::post('logout', 'logout')->middleware('auth:sanctum');
});

// Route::post('users/{user}', [UserController::class, 'updateImage']);

Route::apiResources([
	'vehicles' => VehicleController::class,
	'missions' => MissionController::class,
	'users' => UserController::class
]);
