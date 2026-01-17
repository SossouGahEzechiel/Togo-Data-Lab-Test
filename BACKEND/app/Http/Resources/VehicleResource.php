<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VehicleResource extends JsonResource
{
	public function toArray(Request $request): array
	{
		return [
			'brand' => $this->brand,
			'model' => $this->model,
			'type' => $this->type,
			'registrationNumber' => $this->registration_number,
			'registrationDate' => $this->registration_date,
			'seatsCount' => $this->seats_count,
			'status' => $this->status,
			'reason' => $this->reason,
		];
	}
}
