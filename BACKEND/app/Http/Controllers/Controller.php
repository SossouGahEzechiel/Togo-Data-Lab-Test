<?php

namespace App\Http\Controllers;

use OpenApi\Attributes as OA;

#[OA\Info(
	title: "API Application",
	version: "1.0.0",
	description: "Documentation complète de l'API",
)]
#[OA\Server(
	url: "http://localhost:8000/api",
	description: "Serveur de développement local"
)]
#[OA\SecurityScheme(
	securityScheme: "bearerAuth",
	type: "http",
	scheme: "bearer",
	bearerFormat: "token",
	description: "Authentification par token Bearer"
)]
#[OA\Schema(
	schema: "SuccessResponse",
	properties: [
		new OA\Property(
			property: "message",
			type: "string",
			example: "Opération réussie"
		)
	]
)]
#[OA\Schema(
	schema: "ErrorResponse",
	properties: [
		new OA\Property(
			property: "message",
			type: "string",
			example: "Une erreur est survenue"
		),
		new OA\Property(
			property: "errors",
			type: "object",
			nullable: true,
			additionalProperties: new OA\AdditionalProperties(
				type: "array",
				items: new OA\Items(type: "string")
			)
		)
	]
)]
abstract class Controller
{
}
