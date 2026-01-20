<?php

use App\Http\Controllers\{
	AuthController,
	MissionController,
	ReservationController,
	UserController,
	VehicleController
};
use Illuminate\Support\Facades\Route;

Route::get('/', fn() => _200(env("APP_NAME") . " API is running"));

Route::controller(AuthController::class)->prefix('auth')->group(function () {
	Route::post('login', 'login');
	Route::middleware('auth:sanctum')->group(function () {
		Route::post('logout', 'logout');
		Route::put('configure-password', 'configurePassword');
	});
});

// Route::post('users/{user}', [UserController::class, 'updateImage']);

Route::middleware(['auth:sanctum'])->group(function () {
	Route::controller(VehicleController::class)->prefix('vehicles')->group(function() {
		Route::get('available', 'availableVehicles');
		// Du POST parce que le GET ne permet pas de passer des paramètres
		Route::post('{vehicle}/check-availability', 'checkAvailability');
	});

	Route::apiResources([
		'vehicles' => VehicleController::class,
		'missions' => MissionController::class,
		'users' => UserController::class,
		'reservations' => ReservationController::class
	]);


	Route::controller(ReservationController::class)->prefix('reservations')->group(function () {
		Route::prefix('{reservation}')->group(function () {
			Route::patch('assign-driver', 'assignDriver');
			Route::patch('mark-as-back', 'markAsPassed');
			Route::patch('apply-decision', 'applyDecision');
		});
	});
});
