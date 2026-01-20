<?php

namespace App\Http\Controllers;

use App\Enums\ReservationStatusEnum;
use App\Enums\VehicleStatusEnum;
use App\Http\Resources\ReservationResource;
use App\Models\Reservation;
use App\Models\Vehicle;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use OpenApi\Attributes as OA;
use Throwable;

#[OA\Tag(
	name: "Réservations",
	description: "Gestion des réservations de véhicules"
)]
class ReservationController extends Controller
{
	#[OA\Get(
		path: "/reservations",
		summary: "Lister toutes les réservations",
		description: "Retourne la liste de toutes les réservations triées par date de début et fin",
		tags: ["Réservations"],
		security: [["bearerAuth" => []]],
		responses: [
			new OA\Response(
				response: 200,
				description: "Liste des réservations récupérée avec succès",
				content: new OA\JsonContent(
					type: "array",
					items: new OA\Items(ref: "#/components/schemas/ReservationWithRelations")
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
		$reservations = Reservation::query()
			->orderBy('from')
			->orderBy('to')
			->get();

		return ReservationResource::collection($reservations);
	}

	#[OA\Post(
		path: "/reservations",
		summary: "Créer une nouvelle réservation",
		description: "Crée une nouvelle réservation pour un véhicule et une mission",
		tags: ["Réservations"],
		security: [["bearerAuth" => []]],
		requestBody: new OA\RequestBody(
			required: true,
			content: new OA\JsonContent(
				required: ["from", "missionId", "vehicleId"],
				properties: [
					new OA\Property(
						property: "from",
						type: "string",
						format: "date",
						example: "2024-01-15",
						description: "Date de début de la réservation"
					),
					new OA\Property(
						property: "to",
						type: "string",
						format: "date",
						nullable: true,
						example: "2024-01-20",
						description: "Date de fin de la réservation"
					),
					new OA\Property(
						property: "missionId",
						type: "string",
						format: "uuid",
						example: "550e8400-e29b-41d4-a716-446655440000",
						description: "UUID de la mission"
					),
					new OA\Property(
						property: "vehicleId",
						type: "string",
						format: "uuid",
						example: "550e8400-e29b-41d4-a716-446655440001",
						description: "UUID du véhicule"
					),
					new OA\Property(
						property: "driverId",
						type: "string",
						format: "uuid",
						nullable: true,
						example: "550e8400-e29b-41d4-a716-446655440002",
						description: "UUID du chauffeur (optionnel)"
					)
				]
			)
		),
		responses: [
			new OA\Response(
				response: 201,
				description: "Réservation créée avec succès",
				content: new OA\JsonContent(ref: "#/components/schemas/ReservationWithRelations")
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
	public function store(Request $request): ReservationResource|JsonResponse
	{
		$request->validate([
			'from' => ['required', 'date', 'after_or_equal:today'],
			'to' => ['nullable', 'date', 'after_or_equal:from'],
			'missionId' => ['required', 'exists:missions,id'],
			'vehicleId' => ['required', 'exists:vehicles,id'],
			'driverId' => ['nullable', 'exists:users,id'],
		], [], [
			'from' => "Le début de la réservation",
			'to' => "Le fin de la réservation",
			'missionId' => "La mission",
			'vehicleId' => "Le véhicule",
			'driverId' => "Le chauffeur",
		]);

		$reservation = Reservation::query()->create([
			...$request->only(['from', 'to']),
			'mission_id' => $request->input('missionId'),
			'vehicle_id' => $request->input('vehicleId'),
			'driver_id' => $request->input('driverId'),
			'user_id' => auth()->id(),
			'status' => ReservationStatusEnum::PENDING
		]);
		return new ReservationResource($reservation);
	}

	#[OA\Get(
		path: "/reservations/{id}",
		summary: "Afficher une réservation",
		description: "Retourne les détails d'une réservation spécifique",
		tags: ["Réservations"],
		security: [["bearerAuth" => []]],
		parameters: [
			new OA\Parameter(
				name: "id",
				in: "path",
				required: true,
				description: "UUID de la réservation",
				schema: new OA\Schema(
					type: "string",
					format: "uuid",
					example: "550e8400-e29b-41d4-a716-446655440003"
				)
			)
		],
		responses: [
			new OA\Response(
				response: 200,
				description: "Réservation récupérée avec succès",
				content: new OA\JsonContent(ref: "#/components/schemas/ReservationWithRelations")
			),
			new OA\Response(
				response: 401,
				description: "Non authentifié",
				content: new OA\JsonContent(ref: "#/components/schemas/ErrorResponse")
			),
			new OA\Response(
				response: 404,
				description: "Réservation non trouvée",
				content: new OA\JsonContent(ref: "#/components/schemas/ErrorResponse")
			)
		]
	)]
	public function show(string $id): ReservationResource|JsonResponse
	{
		if (!$reservation = Reservation::query()->find($id)) {
			return _404();
		}

		return new ReservationResource($reservation);
	}

	#[OA\Put(
		path: "/reservations/{id}",
		summary: "Mettre à jour une réservation",
		description: "Met à jour complètement une réservation existante",
		tags: ["Réservations"],
		security: [["bearerAuth" => []]],
		parameters: [
			new OA\Parameter(
				name: "id",
				in: "path",
				required: true,
				description: "UUID de la réservation",
				schema: new OA\Schema(
					type: "string",
					format: "uuid",
					example: "550e8400-e29b-41d4-a716-446655440003"
				)
			)
		],
		requestBody: new OA\RequestBody(
			required: true,
			content: new OA\JsonContent(
				required: ["from", "missionId", "vehicleId"],
				properties: [
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
						example: "2024-01-21"
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
						property: "status",
						type: "string",
						enum: ["En attente", "Validée", "Rejetée", "Passée"],
						nullable: true,
						example: "En attente"
					),
					new OA\Property(
						property: "vehicleStatus",
						type: "string",
						enum: ["available", "reserved", "suspended", "under_repair"],
						nullable: true,
						example: "available",
						description: "Obligatoire si status est 'Passée'"
					),
					new OA\Property(
						property: "vehicleStatusReason",
						type: "string",
						maxLength: 1023,
						nullable: true,
						example: "Véhicule en bon état"
					)
				]
			)
		),
		responses: [
			new OA\Response(
				response: 200,
				description: "Réservation mise à jour avec succès",
				content: new OA\JsonContent(ref: "#/components/schemas/ReservationWithRelations")
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
				description: "Réservation non trouvée",
				content: new OA\JsonContent(ref: "#/components/schemas/ErrorResponse")
			),
			new OA\Response(
				response: 500,
				description: "Erreur interne du serveur",
				content: new OA\JsonContent(ref: "#/components/schemas/ErrorResponse")
			)
		]
	)]
	public function update(Request $request, string $id): ReservationResource|JsonResponse
	{
		if (!$reservation = Reservation::query()->find($id)) {
			return _404();
		}

		$request->validate([
			'from' => ['required', 'date'],
			'to' => ['nullable', 'date'],
			'missionId' => ['required', 'exists:missions,id'],
			'vehicleId' => ['required', 'exists:vehicles,id'],
			'driverId' => ['nullable', 'exists:users,id'],
			'status' => ['nullable', Rule::enum(ReservationStatusEnum::class)],
			'vehicleStatus' => ['required_if:status,' . ReservationStatusEnum::PASSED->value, Rule::enum(VehicleStatusEnum::class)],
			'vehicleStatusReason' => [
				'required_if:vehicleStatus,' . VehicleStatusEnum::SUSPENDED->value . ',' . VehicleStatusEnum::UNDER_REPAIR->value,
				'max:1023'
			]
		], [], [
			'from' => "Le début de la réservation",
			'to' => "Le fin de la réservation",
			'missionId' => "La mission",
			'vehicleId' => "Le véhicule",
			'driverId' => "Le chauffeur",
		]);

		try {
			DB::beginTransaction();
			$reservation->update([
				...$request->only(['from', 'to']),
				'mission_id' => $request->input('missionId'),
				'vehicle_id' => $request->input('vehicleId'),
				'driver_id' => $request->input('driverId'),
				'status' => $status = $request->enum('status', ReservationStatusEnum::class, $reservation->status)
			]);

			if ($status === ReservationStatusEnum::PASSED) {
				$reservation->update([
					'return_date' => now(),
					'status' => $status
				]);

				$reservation->vehicle()->update([
					'status' => $request->enum('vehicleStatus', VehicleStatusEnum::class),
					'reason' => $request->input('vehicleStatusReason')
				]);

				$reservation->refresh();
			}
			DB::commit();
		} catch (Throwable $th) {
			DB::rollBack();
			return _500($th->getMessage());
		}

		return new ReservationResource($reservation);
	}

	#[OA\Delete(
		path: "/reservations/{id}",
		summary: "Supprimer une réservation",
		description: "Supprime une réservation existante",
		tags: ["Réservations"],
		security: [["bearerAuth" => []]],
		parameters: [
			new OA\Parameter(
				name: "id",
				in: "path",
				required: true,
				description: "UUID de la réservation",
				schema: new OA\Schema(
					type: "string",
					format: "uuid",
					example: "550e8400-e29b-41d4-a716-446655440003"
				)
			)
		],
		responses: [
			new OA\Response(
				response: 200,
				description: "Réservation supprimée avec succès",
				content: new OA\JsonContent(ref: "#/components/schemas/SuccessResponse")
			),
			new OA\Response(
				response: 401,
				description: "Non authentifié",
				content: new OA\JsonContent(ref: "#/components/schemas/ErrorResponse")
			),
			new OA\Response(
				response: 404,
				description: "Réservation non trouvée",
				content: new OA\JsonContent(ref: "#/components/schemas/ErrorResponse")
			)
		]
	)]
	public function destroy(string $id): JsonResponse
	{
		if (!$reservation = Reservation::query()->find($id)) {
			return _404();
		}

		$reservation->delete();

		return _200();
	}

	#[OA\Put(
		path: "/reservations/{id}/assign-driver",
		summary: "Assigner un chauffeur à une réservation",
		description: "Assigne un chauffeur à une réservation (si elle n'est pas déjà passée)",
		tags: ["Réservations"],
		security: [["bearerAuth" => []]],
		parameters: [
			new OA\Parameter(
				name: "id",
				in: "path",
				required: true,
				description: "UUID de la réservation",
				schema: new OA\Schema(
					type: "string",
					format: "uuid",
					example: "550e8400-e29b-41d4-a716-446655440003"
				)
			)
		],
		requestBody: new OA\RequestBody(
			required: true,
			content: new OA\JsonContent(
				required: ["driverId"],
				properties: [
					new OA\Property(
						property: "driverId",
						type: "string",
						format: "uuid",
						example: "550e8400-e29b-41d4-a716-446655440002",
						description: "UUID du chauffeur à assigner"
					)
				]
			)
		),
		responses: [
			new OA\Response(
				response: 200,
				description: "Chauffeur assigné avec succès",
				content: new OA\JsonContent(ref: "#/components/schemas/ReservationWithRelations")
			),
			new OA\Response(
				response: 400,
				description: "La réservation est déjà passée",
				content: new OA\JsonContent(ref: "#/components/schemas/ErrorResponse")
			),
			new OA\Response(
				response: 401,
				description: "Non authentifié",
				content: new OA\JsonContent(ref: "#/components/schemas/ErrorResponse")
			),
			new OA\Response(
				response: 404,
				description: "Réservation non trouvée",
				content: new OA\JsonContent(ref: "#/components/schemas/ErrorResponse")
			),
			new OA\Response(
				response: 422,
				description: "Erreur de validation",
				content: new OA\JsonContent(ref: "#/components/schemas/ErrorResponse")
			)
		]
	)]
	public function assignDriver(Request $request, string $id): ReservationResource|JsonResponse
	{
		// TODO: A retirer
		if (!$reservation = Reservation::query()->find($id)) {
			return _404();
		}

		$request->validate([
			'driverId' => ['required', 'exists:users,id'],
		], [], [
			'driverId' => "Le chauffeur"
		]);

		if ($reservation->to && now()->isAfter($reservation->to) || $reservation->status === ReservationStatusEnum::PASSED) {
			return _400("La reservation est déjà passée. Cette action n'est plus possible.");
		}

		$reservation->update([
			'driver_id' => $request->input('driverId'),
		]);

		return new ReservationResource($reservation);
	}

	#[OA\Put(
		path: "/reservations/{id}/mark-as-passed",
		summary: "Marquer une réservation comme terminée",
		description: "Marque une réservation comme terminée et met à jour le statut du véhicule",
		tags: ["Réservations"],
		security: [["bearerAuth" => []]],
		parameters: [
			new OA\Parameter(
				name: "id",
				in: "path",
				required: true,
				description: "UUID de la réservation",
				schema: new OA\Schema(
					type: "string",
					format: "uuid",
					example: "550e8400-e29b-41d4-a716-446655440003"
				)
			)
		],
		requestBody: new OA\RequestBody(
			required: true,
			content: new OA\JsonContent(
				required: ["vehicleStatus"],
				properties: [
					new OA\Property(
						property: "vehicleStatus",
						type: "string",
						enum: ["available", "reserved", "suspended", "under_repair"],
						example: "available",
						description: "Nouveau statut du véhicule après la réservation"
					),
					new OA\Property(
						property: "vehicleStatusReason",
						type: "string",
						maxLength: 1023,
						nullable: true,
						example: "Véhicule en bon état",
						description: "Raison du statut (obligatoire si SUSPENDED ou UNDER_REPAIR)"
					)
				]
			)
		),
		responses: [
			new OA\Response(
				response: 200,
				description: "Réservation marquée comme terminée avec succès",
				content: new OA\JsonContent(ref: "#/components/schemas/ReservationWithRelations")
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
				description: "Réservation non trouvée",
				content: new OA\JsonContent(ref: "#/components/schemas/ErrorResponse")
			),
			new OA\Response(
				response: 500,
				description: "Erreur interne du serveur",
				content: new OA\JsonContent(ref: "#/components/schemas/ErrorResponse")
			)
		]
	)]
	public function markAsPassed(Request $request, string $id): ReservationResource|JsonResponse
	{
		if (!$reservation = Reservation::query()->find($id)) {
			return _404();
		}

		$request->validate([
			'vehicleStatus' => ['required', Rule::enum(VehicleStatusEnum::class)],
			'vehicleStatusReason' => [
				'required_if:vehicleStatus,' . VehicleStatusEnum::SUSPENDED->value . ',' . VehicleStatusEnum::UNDER_REPAIR->value,
				'max:1023'
			]
		]);

		try {
			DB::beginTransaction();
			$reservation->update([
				'return_date' => now(),
				'status' => ReservationStatusEnum::PASSED
			]);

			$reservation->vehicle()->update([
				'status' => $request->enum('vehicleStatus', VehicleStatusEnum::class),
				'reason' => $request->input('vehicleStatusReason')
			]);
			DB::commit();
		} catch (Throwable $th) {
			DB::rollBack();
			return _500($th->getMessage());
		}

		return new ReservationResource($reservation->refresh());
	}

	/**
	 * Logique de validation d'une réservation :
	 * 1. La réservation ne doit pas être déjà PASSED
	 * 2. Le véhicule ne doit pas être UNAVAILABLE ou UNDER_REPAIR
	 * 3. Si validation demandée, le véhicule doit être disponible à la date de début
	 *
	 * Effets si validation réussie :
	 * 1. Statut de la réservation mis à VALIDATED
	 * 2. Véhicule marqué comme RESERVED
	 * 3. Toutes les autres réservations du même véhicule sur la même période sont REJECTED
	 */
	#[OA\Put(
		path: "/reservations/{id}/apply-decision",
		summary: "Appliquer une décision sur une réservation",
		description: "Valide ou rejette une réservation en attente. Si validée, le véhicule est marqué comme réservé et les autres réservations concurrentes sont rejetées.",
		tags: ["Réservations"],
		security: [["bearerAuth" => []]],
		parameters: [
			new OA\Parameter(
				name: "id",
				in: "path",
				required: true,
				description: "UUID de la réservation",
				schema: new OA\Schema(
					type: "string",
					format: "uuid",
					example: "550e8400-e29b-41d4-a716-446655440003"
				)
			)
		],
		requestBody: new OA\RequestBody(
			required: true,
			content: new OA\JsonContent(
				required: ["status"],
				properties: [
					new OA\Property(
						property: "status",
						type: "string",
						enum: ["Validée", "Rejetée"],
						example: "Validée",
						description: "Décision à appliquer à la réservation"
					)
				]
			)
		),
		responses: [
			new OA\Response(
				response: 200,
				description: "Décision appliquée avec succès",
				content: new OA\JsonContent(ref: "#/components/schemas/ReservationWithRelations")
			),
			new OA\Response(
				response: 403,
				description: "Action non autorisée",
				content: new OA\JsonContent(
					oneOf: [
						new OA\Schema(
							properties: [
								new OA\Property(
									property: "message",
									type: "string",
									example: "La reservation est déjà passée. Cette action n'est plus possible."
								)
							]
						),
						new OA\Schema(
							properties: [
								new OA\Property(
									property: "message",
									type: "string",
									example: "Le véhicule associé à la réservation n'est pas disponible."
								)
							]
						)
					]
				)
			),
			new OA\Response(
				response: 401,
				description: "Non authentifié",
				content: new OA\JsonContent(ref: "#/components/schemas/ErrorResponse")
			),
			new OA\Response(
				response: 404,
				description: "Réservation non trouvée",
				content: new OA\JsonContent(ref: "#/components/schemas/ErrorResponse")
			),
			new OA\Response(
				response: 422,
				description: "Erreur de validation",
				content: new OA\JsonContent(ref: "#/components/schemas/ErrorResponse")
			),
			new OA\Response(
				response: 500,
				description: "Erreur interne du serveur",
				content: new OA\JsonContent(ref: "#/components/schemas/ErrorResponse")
			)
		]
	)]
	public function applyDecision(Request $request, string $id): ReservationResource|JsonResponse
	{
		if (!$reservation = Reservation::query()->with('vehicle')->find($id)) {
			return _404();
		}

		if ($reservation->status === ReservationStatusEnum::PASSED) {
			return _403("La reservation est déjà passée. Cette action n'est plus possible.");
		}

		$request->validate([
			'status' => [
				'required',
				Rule::enum(ReservationStatusEnum::class),
				'in:' . ReservationStatusEnum::VALIDATED->value . ',' . ReservationStatusEnum::REJECTED->value,
			],
		]);

		/**
		 * @var Vehicle $vehicle
		 */
		$vehicle = $reservation->vehicle;
		$isValidated = $request->enum('status', ReservationStatusEnum::class) === ReservationStatusEnum::VALIDATED;

		if ($vehicle->status === VehicleStatusEnum::UNAVAILABLE || $vehicle->status === VehicleStatusEnum::UNDER_REPAIR) {
			return _403("Le véhicule associé à la réservation n'est pas disponible.");
		}

		if ($isValidated && !$vehicle->isAvailable($reservation->from, null)) {
			return _403("Le véhicule associé à la réservation n'est pas disponible.");
		}

		try {
			DB::beginTransaction();
			$reservation->update([
				'status' => $request->enum('status', ReservationStatusEnum::class)
			]);

			if ($isValidated) {
				$vehicle->update([
					'status' => VehicleStatusEnum::RESERVED
				]);

				$vehicle->otherReservationExcept($id, $reservation->from, $reservation->to)->update([
					'status' => ReservationStatusEnum::REJECTED
				]);
				// Notifier les autres de la décision
			}
			DB::commit();
			// Notifier l'auteur de la demande que celle-ci a été approuvée ou pas
		} catch (Throwable $th) {
			DB::rollBack();
			return _500($th->getMessage());
		}

		return new ReservationResource($reservation);
	}
}
