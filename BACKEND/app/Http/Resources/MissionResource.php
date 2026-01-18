<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

#[OA\Schema(
	schema: "Mission",
	description: "Schéma de base d'une mission",
	required: ["id", "label", "description", "from"],
	properties: [
		new OA\Property(property: "id", type: "integer", example: 1),
		new OA\Property(property: "label", type: "string", example: "Mission de livraison urgente"),
		new OA\Property(property: "description", type: "string", example: "Livraison de matériel médical"),
		new OA\Property(
			property: "from",
			type: "string",
			format: "date",
			example: "2024-01-15"
		),
		new OA\Property(
			property: "to",
			type: "string",
			format: "date",
			nullable: true,
			example: "2024-01-20"
		)
	]
)]

#[OA\Schema(
	schema: "MissionWithRelations",
	description: "Mission avec toutes ses relations",
	allOf: [
		new OA\Schema(ref: "#/components/schemas/Mission"),
		new OA\Schema(
			properties: [
				new OA\Property(
					property: "vehicles",
					type: "array",
					items: new OA\Items(ref: "#/components/schemas/Vehicle"),
					description: "Véhicules affectés à la mission",
					nullable: true
				),
				new OA\Property(
					property: "author",
					type: "array",
					items: new OA\Items(ref: "#/components/schemas/User"),
					description: "Utilisateurs ayant fait des réservations pour la mission",
					nullable: true
				),
				new OA\Property(
					property: "drivers",
					type: "array",
					items: new OA\Items(ref: "#/components/schemas/User"),
					description: "Chauffeurs affectés à la mission",
					nullable: true
				)
			]
		)
	]
)]
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
