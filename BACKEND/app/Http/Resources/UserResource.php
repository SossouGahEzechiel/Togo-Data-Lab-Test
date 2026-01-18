<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

#[OA\Schema(
	schema: "User",
	description: "Schéma de données d'un utilisateur",
	properties: [
		new OA\Property(
			property: "id",
			type: "string",
			format: "uuid",
			example: "550e8400-e29b-41d4-a716-446655440002"
		),
		new OA\Property(property: "firstName", type: "string", example: "John"),
		new OA\Property(property: "lastName", type: "string", example: "Doe"),
		new OA\Property(property: "email", type: "string", format: "email", example: "john.doe@example.com"),
		new OA\Property(property: "role", type: "string", example: "user"),
		new OA\Property(property: "phone", type: "string", nullable: true, example: "+33123456789"),
		new OA\Property(property: "isActive", type: "boolean", example: true),
		new OA\Property(property: "image", ref: "#/components/schemas/Image", nullable: true),
		new OA\Property(
			property: "token",
			type: "string",
			nullable: true,
			example: "1|abcdefgh1234567890"
		)
	]
)]
class UserResource extends JsonResource
{
	public function toArray(Request $request): array
	{
		return [
			'id' => $this->id,
			'firstName' => $this->first_name,
			'lastName' => $this->last_name,
			'email' => $this->email,
			'role' => $this->role,
			'phone' => $this->phone,
			'isActive' => $this->is_active,
			'image' => new ImageResource($this->whenLoaded('image')),
			'token' => $this->whenHas('token')
		];
	}
}
