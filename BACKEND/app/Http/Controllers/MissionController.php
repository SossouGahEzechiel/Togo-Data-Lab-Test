<?php

namespace App\Http\Controllers;

use App\Http\Resources\MissionResource;
use App\Models\Mission;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use OpenApi\Attributes as OA;

#[OA\Tag(
	name: "Missions",
	description: "Gestion des missions"
)]
class MissionController extends Controller
{
	#[OA\Get(
		path: "/missions",
		summary: "Lister toutes les missions",
		description: "Retourne la liste de toutes les missions triées par date de début et libellé",
		tags: ["Missions"],
		security: [["bearerAuth" => []]],
		responses: [
			new OA\Response(
				response: 200,
				description: "Liste des missions récupérée avec succès",
				content: new OA\JsonContent(
					type: "array",
					items: new OA\Items(ref: "#/components/schemas/Mission")
				)
			),
			new OA\Response(
				response: 401,
				description: "Non authentifié",
				content: new OA\JsonContent(ref: "#/components/schemas/ErrorResponse")
			)
		]
	)]
	public function index(): AnonymousResourceCollection
	{
		$missions = Mission::query()
			->orderBy('from')
			->orderBy('label')
			->get();

		return MissionResource::collection($missions);
	}

	#[OA\Post(
		path: "/missions",
		summary: "Créer une nouvelle mission",
		description: "Crée une nouvelle mission avec les informations fournies",
		tags: ["Missions"],
		security: [["bearerAuth" => []]],
		requestBody: new OA\RequestBody(
			required: true,
			content: new OA\JsonContent(
				required: ["label", "description", "from"],
				properties: [
					new OA\Property(
						property: "label",
						type: "string",
						maxLength: 255,
						example: "Mission de livraison urgente",
						description: "Libellé de la mission"
					),
					new OA\Property(
						property: "description",
						type: "string",
						example: "Livraison de matériel médical vers l'hôpital central",
						description: "Description détaillée de la mission"
					),
					new OA\Property(
						property: "from",
						type: "string",
						format: "date",
						example: "2024-01-15",
						description: "Date de début de la mission"
					),
					new OA\Property(
						property: "to",
						type: "string",
						format: "date",
						nullable: true,
						example: "2024-01-20",
						description: "Date de fin de la mission (optionnelle)"
					)
				]
			)
		),
		responses: [
			new OA\Response(
				response: 201,
				description: "Mission créée avec succès",
				content: new OA\JsonContent(ref: "#/components/schemas/Mission")
			),
			new OA\Response(
				response: 400,
				description: "Données invalides",
				content: new OA\JsonContent(ref: "#/components/schemas/ErrorResponse")
			),
			new OA\Response(
				response: 401,
				description: "Non authentifié",
				content: new OA\JsonContent(ref: "#/components/schemas/ErrorResponse")
			),
			new OA\Response(
				response: 422,
				description: "Erreur de validation",
				content: new OA\JsonContent(ref: "#/components/schemas/ErrorResponse")
			)
		]
	)]
	public function store(Request $request): MissionResource|JsonResponse
	{
		$data = $request->validate([
			'label' => ['required', 'string', 'max:255'],
			'description' => ['required', 'string'],
			'from' => ['required', 'date'],
			'to' => ['nullable', 'date'],
		], [], [
			'label' => "Le libellé de la mission",
			'description' => "La description de la mission",
			'from' => "La date de début de la mission",
			'to' => "La date de fin de la mission"
		]);

		$mission = Mission::query()->create($data);
		return new MissionResource($mission);
	}

	#[OA\Get(
		path: "/missions/{id}",
		summary: "Afficher une mission",
		description: "Retourne les détails d'une mission spécifique avec ses véhicules, utilisateurs et chauffeurs associés",
		tags: ["Missions"],
		security: [["bearerAuth" => []]],
		parameters: [
			new OA\Parameter(
				name: "id",
				in: "path",
				required: true,
				description: "UUID de la mission",
				schema: new OA\Schema(
					type: "string",
					format: "uuid",
					example: "550e8400-e29b-41d4-a716-446655440000"
				)
			)
		],
		responses: [
			new OA\Response(
				response: 200,
				description: "Mission récupérée avec succès",
				content: new OA\JsonContent(ref: "#/components/schemas/MissionWithRelations")
			),
			new OA\Response(
				response: 401,
				description: "Non authentifié",
				content: new OA\JsonContent(ref: "#/components/schemas/ErrorResponse")
			),
			new OA\Response(
				response: 404,
				description: "Mission non trouvée",
				content: new OA\JsonContent(ref: "#/components/schemas/ErrorResponse")
			)
		]
	)]
	public function show(string $id): MissionResource|JsonResponse
	{
		if (!$mission = Mission::query()->with(['vehicles', 'users', 'drivers'])->find($id)) {
			return _404();
		}
		return new MissionResource($mission);
	}

	#[OA\Put(
		path: "/missions/{id}",
		summary: "Mettre à jour une mission",
		description: "Met à jour les informations d'une mission existante",
		tags: ["Missions"],
		security: [["bearerAuth" => []]],
		parameters: [
			new OA\Parameter(
				name: "id",
				in: "path",
				required: true,
				description: "UUID de la mission",
				schema: new OA\Schema(
					type: "string",
					format: "uuid",
					example: "550e8400-e29b-41d4-a716-446655440000"
				)
			)
		],
		requestBody: new OA\RequestBody(
			required: true,
			content: new OA\JsonContent(
				required: ["label", "description", "from"],
				properties: [
					new OA\Property(
						property: "label",
						type: "string",
						maxLength: 255,
						example: "Mission de livraison urgente - Mise à jour"
					),
					new OA\Property(
						property: "description",
						type: "string",
						example: "Livraison de matériel médical vers l'hôpital central - Priorité haute"
					),
					new OA\Property(
						property: "from",
						type: "string",
						format: "date",
						example: "2024-01-16"
					),
					new OA\Property(
						property: "to",
						type: "string",
						format: "date",
						nullable: true,
						example: "2024-01-22"
					)
				]
			)
		),
		responses: [
			new OA\Response(
				response: 200,
				description: "Mission mise à jour avec succès",
				content: new OA\JsonContent(ref: "#/components/schemas/MissionWithRelations")
			),
			new OA\Response(
				response: 400,
				description: "Données invalides",
				content: new OA\JsonContent(ref: "#/components/schemas/ErrorResponse")
			),
			new OA\Response(
				response: 401,
				description: "Non authentifié",
				content: new OA\JsonContent(ref: "#/components/schemas/ErrorResponse")
			),
			new OA\Response(
				response: 404,
				description: "Mission non trouvée",
				content: new OA\JsonContent(ref: "#/components/schemas/ErrorResponse")
			),
			new OA\Response(
				response: 422,
				description: "Erreur de validation",
				content: new OA\JsonContent(ref: "#/components/schemas/ErrorResponse")
			)
		]
	)]
	public function update(Request $request, string $id): MissionResource|JsonResponse
	{
		$mission = Mission::query()->find($id);
		if (!$mission) {
			return _404();
		}

		$data = $request->validate([
			'label' => ['required', 'string', 'max:255'],
			'description' => ['required', 'string'],
			'from' => ['required', 'date'],
			'to' => ['nullable', 'date'],
		], [], [
			'label' => "Le libellé de la mission",
			'description' => "La description de la mission",
			'from' => "La date de début de la mission",
			'to' => "La date de fin de la mission"
		]);

		$mission->update($data);
		return new MissionResource($mission->load(['vehicles.images', 'users', 'drivers']));
	}

	#[OA\Delete(
		path: "/missions/{id}",
		summary: "Supprimer une mission",
		description: "Supprime une mission si elle n'a pas de réservations associées",
		tags: ["Missions"],
		security: [["bearerAuth" => []]],
		parameters: [
			new OA\Parameter(
				name: "id",
				in: "path",
				required: true,
				description: "UUID de la mission",
				schema: new OA\Schema(
					type: "string",
					format: "uuid",
					example: "550e8400-e29b-41d4-a716-446655440000"
				)
			)
		],
		responses: [
			new OA\Response(
				response: 200,
				description: "Mission supprimée avec succès",
				content: new OA\JsonContent(ref: "#/components/schemas/SuccessResponse")
			),
			new OA\Response(
				response: 400,
				description: "Impossible de supprimer (réservations existantes)",
				content: new OA\JsonContent(ref: "#/components/schemas/ErrorResponse")
			),
			new OA\Response(
				response: 401,
				description: "Non authentifié",
				content: new OA\JsonContent(ref: "#/components/schemas/ErrorResponse")
			),
			new OA\Response(
				response: 404,
				description: "Mission non trouvée",
				content: new OA\JsonContent(ref: "#/components/schemas/ErrorResponse")
			)
		]
	)]
	public function destroy(string $id): JsonResponse
	{
		$mission = Mission::query()->find($id);
		if (!$mission) {
			return _404();
		}

		if ($mission->reservations()->count() > 0) {
			return _400();
		}

		$mission->delete();
		return _200();
	}
}
