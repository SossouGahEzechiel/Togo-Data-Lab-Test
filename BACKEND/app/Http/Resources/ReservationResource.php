<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

#[OA\Schema(
	schema: "Reservation",
	description: "Schéma de base d'une réservation",
	required: ["id", "from", "missionId", "vehicleId", "userId", "status"],
	properties: [
		new OA\Property(
			property: "id",
			type: "string",
			format: "uuid",
			example: "550e8400-e29b-41d4-a716-446655440003"
		),
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
		),
		new OA\Property(
			property: "missionId",
			type: "string",
			format: "uuid",
			example: "550e8400-e29b-41d4-a716-446655440000"
		),
		new OA\Property(
			property: "vehicleId",
			type: "string",
			format: "uuid",
			example: "550e8400-e29b-41d4-a716-446655440001"
		),
		new OA\Property(
			property: "driverId",
			type: "string",
			format: "uuid",
			nullable: true,
			example: "550e8400-e29b-41d4-a716-446655440002"
		),
		new OA\Property(
			property: "userId",
			type: "string",
			format: "uuid",
			example: "550e8400-e29b-41d4-a716-446655440004"
		),
		new OA\Property(
			property: "status",
			type: "string",
			enum: ["En attente", "Validée", "Rejetée", "Passée"],
			example: "En attente"
		),
		new OA\Property(
			property: "returnDate",
			type: "string",
			format: "date-time",
			nullable: true,
			example: "2024-01-20T15:30:00Z"
		),
	]
)]

#[OA\Schema(
	schema: "ReservationWithRelations",
	description: "Réservation avec toutes ses relations",
	allOf: [
		new OA\Schema(ref: "#/components/schemas/Reservation"),
		new OA\Schema(
			properties: [
				new OA\Property(
					property: "mission",
					ref: "#/components/schemas/Mission",
					nullable: true
				),
				new OA\Property(
					property: "vehicle",
					ref: "#/components/schemas/Vehicle",
					nullable: true
				),
				new OA\Property(
					property: "driver",
					ref: "#/components/schemas/User",
					nullable: true
				),
				new OA\Property(
					property: "user",
					ref: "#/components/schemas/User",
					nullable: true
				)
			]
		)
	]
)]
class ReservationResource extends JsonResource
{
	public function toArray(Request $request): array
	{
		return [
			'id' => $this->id,
			'from' => $this->from->format('Y-m-d'),
			'to' => $this->to?->format('Y-m-d'),
			'mission' => new MissionResource($this->mission),
			'vehicle' => new VehicleResource($this->vehicle),
			'driver' => new UserResource($this->driver),
			'user' => new UserResource($this->author),
			'status' => $this->status,
			'returnDate' => $this->return_date?->format('Y-m-d H:i:s')
		];
	}
}
