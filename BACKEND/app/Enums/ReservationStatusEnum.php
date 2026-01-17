<?php

namespace App\Enums;

enum ReservationStatusEnum: string
{
	case PENDING = "En attente";
	case VALIDATED = "Validée";
	case REJECTED = "Rejetée";
	case PASSED = "Passée";
}
