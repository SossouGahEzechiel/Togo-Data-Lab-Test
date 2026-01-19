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
			property: "id",
			type: "string",
			format: "uuid",
			example: "d4fb2906-11b7-4512-a292-1ec5d46351e2"
		),
		new OA\Property(
			property: "path",
			type: "string",
			description: "Chemin d'accès à l'image",
			example: "path/to/file.jpg"
		)
	]
)]
class ImageResource extends JsonResource
{
	public function toArray(Request $request): array
	{
		return [
			'id' => $this->id,
			'path' => $this->path
		];
	}
}
