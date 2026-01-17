<?php

namespace App\Enums;

use App\Traits\EnumToolsTrait;

enum VehicleStatusEnum: String {

	use EnumToolsTrait;

	case AVAILABLE = "Disponible";
	case UNAVAILABLE = "Indisponible";
	case UNDER_REPAIR = "En réparation";
	case RESERVED = "Réservé";
	case SUSPENDED = "Suspendu";
}

