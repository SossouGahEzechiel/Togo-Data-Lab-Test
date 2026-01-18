<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

#[OA\Schema(
	schema: "User",
	title: "Utilisateur",
	description: "Schéma de données d'un utilisateur",
	required: ["id", "firstName", "lastName", "email", "role", "isActive"],
	properties: [
		new OA\Property(property: "id", format: "uuid", example: "123e4567-e89b-12d3-a456-426655440000"),
		new OA\Property(property: "firstName", type: "string", example: "John"),
		new OA\Property(property: "lastName", type: "string", example: "Doe"),
		new OA\Property(property: "email", type: "string", format: "email", example: "john.doe@example.com"),
		new OA\Property(property: "role", type: "string", enum: ["admin", "user", "moderator"], example: "user"),
		new OA\Property(property: "phone", type: "string", nullable: true, example: "+33123456789"),
		new OA\Property(property: "isActive", type: "boolean", example: true),
		new OA\Property(property: "image", ref: "#/components/schemas/Image", nullable: true),
	]
)]
#[OA\Schema(
	schema: "UserWithToken",
	title: "Utilisateur avec token",
	description: "Schéma de données d'un utilisateur avec token d'authentification",
	allOf: [
		new OA\Schema(ref: "#/components/schemas/User"),
		new OA\Schema(
			properties: [
				new OA\Property(
					property: "token",
					type: "string",
					description: "Token d'authentification Bearer",
					example: "9a0866e5-649f-4fd0-88f1-259337e3b603|abcdefgh1234567890"
				)
			]
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
