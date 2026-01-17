<?php

namespace App\Enums;

use App\Traits\EnumToolsTrait;

enum UserRoleEnum: string {

	use EnumToolsTrait;

	case ADMIN = "Administrateur";
	case EMPLOYEE = "Employé";
	case DRIVER = "Chauffeur";
}
