<?php

namespace App\Enums;

use Illuminate\Support\Traits\EnumeratesValues;

enum VehicleStatusEnum: String {

	use EnumeratesValues;

	case AVAILABLE = "Disponible";
	case UNAVAILABLE = "Indisponible";
	case UNDER_REPAIR = "En réparation";
	case RESERVED = "Réservé";
	case SUSPENDED = "Suspendu";
}
