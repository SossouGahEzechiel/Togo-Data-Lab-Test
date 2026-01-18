<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

#[OA\Schema(
	schema: "Image",
	title: "Image",
	description: "Schéma de données d'une image",
	required: ["path"],
	properties: [
		new OA\Property(
			property: "path",
			type: "string",
			description: "Chemin d'accès à l'image",
			example: "images/users/profile.jpg"
		)
	]
)]
class ImageResource extends JsonResource
{
	public function toArray(Request $request): array
	{
		return [
			'path' => $this->path
		];
	}
}
