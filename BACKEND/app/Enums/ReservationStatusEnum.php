<?php

namespace App\Enums;

use App\Traits\EnumToolsTrait;

enum ReservationStatusEnum: string
{
	use EnumToolsTrait;

	case PENDING = "En attente";
	case VALIDATED = "Validée";
	case REJECTED = "Rejetée";
	case PASSED = "Passée";
}
