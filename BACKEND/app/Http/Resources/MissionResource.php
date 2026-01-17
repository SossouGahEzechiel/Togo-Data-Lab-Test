<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MissionResource extends JsonResource
{
	public function toArray(Request $request): array
	{
		return [
			'id' => $this->id,
			'label' => $this->label,
			'description' => $this->description,
			'from' => $this->from->format('Y-m-d'),
			'to' => $this->to?->format('Y-m-d'),
			'vehicles' => VehicleResource::collection($this->whenLoaded('vehicles'))
		];
	}
}
