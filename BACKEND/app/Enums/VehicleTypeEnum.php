<?php

namespace App\Enums;

use App\Traits\EnumToolsTrait;

enum VehicleTypeEnum: String {

	use EnumToolsTrait;

	case CAR = "Voiture";
	case MOTOR_CYCLE = "Moto";
	case PICK_UP = "Pick up";
}
