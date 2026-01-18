<?php

namespace App\Http\Controllers;

use App\Enums\{VehicleStatusEnum, VehicleTypeEnum};
use App\Http\Resources\VehicleResource;
use App\Models\Image;
use App\Models\Vehicle;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use OpenApi\Attributes as OA;
use Throwable;

#[OA\Tag(
	name: "Véhicules",
	description: "Gestion des véhicules"
)]
class VehicleController extends Controller
{
	private const FOLDER_NAME = 'vehicles';

	#[OA\Get(
		path: "/vehicles",
		summary: "Lister tous les véhicules",
		description: "Retourne la liste de tous les véhicules avec leurs images",
		tags: ["Véhicules"],
		security: [["bearerAuth" => []]],
		responses: [
			new OA\Response(
				response: 200,
				description: "Liste des véhicules récupérée avec succès",
				content: new OA\JsonContent(
					type: "array",
					items: new OA\Items(ref: "#/components/schemas/Vehicle")
				)
			)
		]
	)]
	public function index(): AnonymousResourceCollection
	{
		$vehicles = Vehicle::query()
			->with('images')
			->orderBy('registration_number')
			->get();

		return VehicleResource::collection($vehicles);
	}

	#[OA\Post(
		path: "/vehicles",
		summary: "Créer un nouveau véhicule",
		description: "Crée un nouveau véhicule avec éventuellement des images",
		tags: ["Véhicules"],
		security: [["bearerAuth" => []]],
		requestBody: new OA\RequestBody(
			required: true,
			content: [
				new OA\MediaType(
					mediaType: "multipart/form-data",
					schema: new OA\Schema(
						required: [
							"brand",
							"model",
							"type",
							"registrationNumber",
							"registrationDate",
							"seatsCount",
							"status"
						],
						properties: [
							new OA\Property(
								property: "brand",
								type: "string",
								minLength: 4,
								maxLength: 31,
								example: "Toyota",
								description: "Marque du véhicule"
							),
							new OA\Property(
								property: "model",
								type: "string",
								minLength: 4,
								maxLength: 31,
								example: "Corolla",
								description: "Modèle du véhicule"
							),
							new OA\Property(
								property: "type",
								type: "string",
								enum: ["Voiture", "Moto", "Pick up", "Mini bus", "Bus"],
								example: "Voiture",
								description: "Type de véhicule"
							),
							new OA\Property(
								property: "registrationNumber",
								type: "string",
								minLength: 8,
								maxLength: 31,
								example: "AB-123-CD",
								description: "Numéro d'immatriculation"
							),
							new OA\Property(
								property: "registrationDate",
								type: "string",
								format: "date",
								example: "2023-01-15",
								description: "Date de première immatriculation"
							),
							new OA\Property(
								property: "seatsCount",
								type: "integer",
								minimum: 1,
								maximum: 15,
								example: 5,
								description: "Nombre de places assises"
							),
							new OA\Property(
								property: "status",
								type: "string",
								enum: ["Disponible", "Indisponible", "En réparation", "Réservé", "Suspendu"],
								example: "Disponible",
								description: "Statut du véhicule"
							),
							new OA\Property(
								property: "reason",
								type: "string",
								maxLength: 1023,
								nullable: true,
								example: "En révision technique",
								description: "Raison du statut (obligatoire si SUSPENDED ou UNDER_REPAIR)"
							),
							new OA\Property(
								property: "images[]",
								type: "array",
								items: new OA\Items(
									type: "string",
									format: "binary"
								),
								description: "Images du véhicule (formats: jpeg, jpg, png, webp, max: 2MB)",
								maxItems: 10
							)
						]
					)
				)
			]
		),
		responses: [
			new OA\Response(
				response: 201,
				description: "Véhicule créé avec succès",
				content: new OA\JsonContent(ref: "#/components/schemas/Vehicle")
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
	public function store(Request $request): VehicleResource|JsonResponse
	{
		$request->validate([
			'brand' => ['required', 'min:4', 'max:31'],
			'model' => ['required', 'min:4', 'max:31'],
			'type' => ['required', Rule::enum(VehicleTypeEnum::class)],
			'registrationNumber' => [
				'required',
				Rule::unique(Vehicle::class, 'registration_number'),
				'min:8',
				'max:31'
			],
			'registrationDate' => ['required', 'date'],
			'seatsCount' => ['required', 'numeric', 'min:1', 'max:15'],
			'status' => ['required', Rule::enum(VehicleStatusEnum::class)],
			'reason' => [
				'required_if:status,' . VehicleStatusEnum::SUSPENDED->value . ',' . VehicleStatusEnum::UNDER_REPAIR->value,
				'max:1023'
			],
			'images.*' => ['nullable', 'file', 'mimes:jpeg,jpg,png,webp', 'max:2048']
		], [], [
			'brand' => "La marque du véhicule",
			'model' => "Le modèle du véhicule",
			'type' => "Le type du véhicule",
			'registrationNumber' => "L'immatriculation",
			'registrationDate' => "La date d'enregistrement",
			'seatsCount' => "Le nombre de sièges",
			'status' => "Le statut du véhicule",
			'reason' => "La raison du statut",
			'images.*' => "Les images du véhicule",
		]);

		$vehicle = Vehicle::query()->create([
			...$request->except(['registrationNumber', 'registrationDate', 'seatsCount', 'images']),
			'registration_number' => $request->input('registrationNumber'),
			'registration_date' => $request->date('registrationDate'),
			'seats_count' => $request->integer('seatsCount')
		]);

		$images = $request->file('images');

		foreach ($images as $image) {
			$extension = $image->getClientOriginalExtension();
			$path = '';
			try {
				DB::beginTransaction();
				$path = $image->storeAs(static::FOLDER_NAME, uniqid() . '.' . $extension, 'public');
				$vehicle->images()->create(['path' => $path]);
				DB::commit();
				$path = '';
			} catch (Throwable $th) {
				DB::rollBack();
				delete_file($path);
			}
		}

		return new VehicleResource($vehicle->load('images'));
	}

	#[OA\Get(
		path: "/vehicles/{id}",
		summary: "Afficher un véhicule",
		description: "Retourne les détails d'un véhicule spécifique",
		tags: ["Véhicules"],
		security: [["bearerAuth" => []]],
		parameters: [
			new OA\Parameter(
				name: "id",
				in: "path",
				required: true,
				description: "ID du véhicule",
				schema: new OA\Schema(type: "integer", example: 1)
			)
		],
		responses: [
			new OA\Response(
				response: 200,
				description: "Véhicule récupéré avec succès",
				content: new OA\JsonContent(ref: "#/components/schemas/Vehicle")
			),
			new OA\Response(
				response: 404,
				description: "Véhicule non trouvé",
				content: new OA\JsonContent(ref: "#/components/schemas/ErrorResponse")
			)
		]
	)]
	public function show(string $id): VehicleResource|JsonResponse
	{
		if (!$vehicle = Vehicle::query()->find($id)) {
			return _404();
		}

		return new VehicleResource($vehicle);
	}

	#[OA\Put(
		path: "/vehicles/{id}",
		summary: "Mettre à jour un véhicule",
		description: "Met à jour les informations d'un véhicule existant",
		tags: ["Véhicules"],
		security: [["bearerAuth" => []]],
		parameters: [
			new OA\Parameter(
				name: "id",
				in: "path",
				required: true,
				description: "ID du véhicule",
				schema: new OA\Schema(type: "integer", example: 1)
			)
		],
		requestBody: new OA\RequestBody(
			required: true,
			content: new OA\JsonContent(
				required: ["brand", "model", "type", "registrationNumber", "registrationDate", "seatsCount", "status"],
				properties: [
					new OA\Property(
						property: "brand",
						type: "string",
						minLength: 4,
						maxLength: 31,
						example: "Toyota"
					),
					new OA\Property(
						property: "model",
						type: "string",
						minLength: 4,
						maxLength: 31,
						example: "Corolla Hybrid"
					),
					new OA\Property(
						property: "type",
						type: "string",
						enum: ["car", "motorcycle", "truck", "van", "bus"],
						example: "car"
					),
					new OA\Property(
						property: "registrationNumber",
						type: "string",
						minLength: 8,
						maxLength: 31,
						example: "AB-123-CD"
					),
					new OA\Property(
						property: "registrationDate",
						type: "string",
						format: "date",
						example: "2023-01-15"
					),
					new OA\Property(
						property: "seatsCount",
						type: "integer",
						minimum: 1,
						maximum: 15,
						example: 5
					),
					new OA\Property(
						property: "status",
						type: "string",
						enum: ["available", "reserved", "suspended", "under_repair"],
						example: "available"
					),
					new OA\Property(
						property: "reason",
						type: "string",
						maxLength: 1023,
						nullable: true,
						example: "Véhicule en maintenance"
					)
				]
			)
		),
		responses: [
			new OA\Response(
				response: 200,
				description: "Véhicule mis à jour avec succès",
				content: new OA\JsonContent(ref: "#/components/schemas/Vehicle")
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
				description: "Véhicule non trouvé",
				content: new OA\JsonContent(ref: "#/components/schemas/ErrorResponse")
			),
			new OA\Response(
				response: 422,
				description: "Erreur de validation",
				content: new OA\JsonContent(ref: "#/components/schemas/ErrorResponse")
			)
		]
	)]
	public function update(Request $request, string $id): VehicleResource|JsonResponse
	{
		if (!$vehicle = Vehicle::query()->find($id)) {
			return _404();
		}

		$request->validate([
			'brand' => ['required', 'min:4', 'max:31'],
			'model' => ['required', 'min:4', 'max:31'],
			'type' => ['required', Rule::enum(VehicleTypeEnum::class)],
			'registrationNumber' => [
				'required',
				Rule::unique(Vehicle::class)->ignore($id, 'registration_number'),
				'min:8',
				'max:31'
			],
			'registrationDate' => ['required', 'date'],
			'seatsCount' => ['required', 'numeric', 'integer', 'min:1', 'max:15'],
			'status' => ['required', Rule::enum(VehicleStatusEnum::class)],
			'reason' => [
				'required_if:status,' . VehicleStatusEnum::SUSPENDED->value . ',' . VehicleStatusEnum::UNDER_REPAIR->value,
				'max:1023'
			],
		], [], [
			'brand' => "La marque du véhicule",
			'model' => "Le modèle du véhicule",
			'type' => "Le type du véhicule",
			'registrationNumber' => "L'immatriculation",
			'registrationDate' => "La date d'enregistrement",
			'seatsCount' => "Le nombre de sièges",
			'status' => "Le statut du véhicule",
			'reason' => "La raison du statut",
		]);

		$vehicle->update([
			...$request->except(['registrationNumber', 'registrationDate', 'seatsCount', 'images']),
			'registration_number' => $request->input('registrationNumber'),
			'registration_date' => $request->date('registrationDate'),
			'seats_count' => $request->integer('seatsCount')
		]);
		return new VehicleResource($vehicle);
	}

	#[OA\Delete(
		path: "/vehicles/{id}",
		summary: "Supprimer un véhicule",
		description: "Supprime un véhicule et ses images associées",
		tags: ["Véhicules"],
		security: [["bearerAuth" => []]],
		parameters: [
			new OA\Parameter(
				name: "id",
				in: "path",
				required: true,
				description: "ID du véhicule",
				schema: new OA\Schema(type: "integer", example: 1)
			)
		],
		responses: [
			new OA\Response(
				response: 200,
				description: "Véhicule supprimé avec succès",
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
				description: "Véhicule non trouvé",
				content: new OA\JsonContent(ref: "#/components/schemas/ErrorResponse")
			),
			new OA\Response(
				response: 500,
				description: "Erreur interne du serveur",
				content: new OA\JsonContent(ref: "#/components/schemas/ErrorResponse")
			)
		]
	)]
	public function destroy(string $id): JsonResponse
	{
		if (!$vehicle = Vehicle::query()->with('images')->find($id)) {
			return _404();
		}

		if ($vehicle->reservations()->count() > 0) {
			return _400();
		}

		try {
			$vehicle->images->each(function (Image $image) {
				delete_file($image->path);
				$image->delete();
			});

			$vehicle->delete();
		} catch (Throwable $th) {
			return _500($th->getMessage());
		}
		return _200();
	}
}
