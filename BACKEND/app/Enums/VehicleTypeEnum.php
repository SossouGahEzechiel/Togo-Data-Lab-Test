<?php

namespace App\Enums;

use Illuminate\Support\Traits\EnumeratesValues;

enum VehicleTypeEnum: String {

	use EnumeratesValues;

	case CAR = "Voiture";
	case MOTOR_CYCLE = "Moto";
	case PICK_UP = "Pick up";
}
