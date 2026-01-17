<?php

namespace App\Enums;

use App\Traits\EnumToolsTrait;

enum VehicleTypeEnum: String {

	use EnumToolsTrait;

	case SUV = "Voiture";
	case MOTOR_CYCLE = "Moto";
	case PICKUP = "Pick up";
	case MINIBUS = "Mini bus";
	case BUS = "Bus";
}
