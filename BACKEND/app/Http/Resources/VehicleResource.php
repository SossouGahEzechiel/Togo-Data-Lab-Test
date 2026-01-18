<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

#[OA\Schema(
	schema: "Vehicle",
	description: "Schéma de données d'un véhicule",
	required: ["id", "brand", "model", "type", "registrationNumber", "registrationDate", "seatsCount", "status"],
	properties: [
		new OA\Property(property: "id", type: "integer", example: 1),
		new OA\Property(property: "brand", type: "string", example: "Toyota"),
		new OA\Property(property: "model", type: "string", example: "Corolla"),
		new OA\Property(
			property: "type",
			type: "string",
			enum: ["car", "motorcycle", "truck", "van", "bus"],
			example: "car"
		),
		new OA\Property(property: "registrationNumber", type: "string", example: "TG G/A 1024"),
		new OA\Property(property: "registrationDate", type: "string", format: "date", example: "2023-01-15"),
		new OA\Property(property: "seatsCount", type: "integer", example: 5),
		new OA\Property(
			property: "status",
			type: "string",
			enum: ["available", "reserved", "suspended", "under_repair"],
			example: "available"
		),
		new OA\Property(property: "reason", type: "string", nullable: true, example: "En révision technique"),
		new OA\Property(
			property: "images",
			type: "array",
			items: new OA\Items(ref: "#/components/schemas/Image"),
			nullable: true
		)
	]
)]
class VehicleResource extends JsonResource
{
	public function toArray(Request $request): array
	{
		return [
			'id' => $this->id,
			'brand' => $this->brand,
			'model' => $this->model,
			'type' => $this->type,
			'registrationNumber' => $this->registration_number,
			'registrationDate' => $this->registration_date,
			'seatsCount' => $this->seats_count,
			'status' => $this->status,
			'reason' => $this->reason,
			'images' => ImageResource::collection($this->whenLoaded('images')),
		];
	}
}
