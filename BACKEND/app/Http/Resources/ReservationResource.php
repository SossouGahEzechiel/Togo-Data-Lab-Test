<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReservationResource extends JsonResource
{
	public function toArray(Request $request): array
	{
		return [
			'id' => $this->id,
			'from' => $this->from,
			'to' => $this->to,
			'status' => $this->status,
			'missionId' => $this->mission_id,
			'vehicleId' => $this->vehicle_id,
			'driverId' => $this->driver_id,
			'userId' => $this->user_id,
			'mission' => new MissionResource($this->mission),
			'vehicle' => new VehicleResource($this->vehicle),
			'driver' => new UserResource($this->driver),
			'user' => new UserResource($this->author),
		];
	}
}
