<?php

namespace App\Http\Controllers;

use App\Enums\UserRoleEnum;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Notifications\NewUserCredentialsNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use OpenApi\Attributes as OA;
use Throwable;

#[OA\Tag(
	name: "Utilisateurs",
	description: "Gestion des utilisateurs du système"
)]
class UserController extends Controller
{
	private const IMAGE_FOLDER = 'users';

	#[OA\Get(
		path: "/users",
		summary: "Lister tous les utilisateurs",
		description: "Retourne la liste de tous les utilisateurs avec leurs images, triés par nom et prénom",
		tags: ["Utilisateurs"],
		security: [["bearerAuth" => []]],
		responses: [
			new OA\Response(
				response: 200,
				description: "Liste des utilisateurs récupérée avec succès",
				content: new OA\JsonContent(
					type: "array",
					items: new OA\Items(ref: "#/components/schemas/User")
				)
			),
			new OA\Response(
				response: 401,
				description: "Non authentifié",
				content: new OA\JsonContent(ref: "#/components/schemas/ErrorResponse")
			),
			new OA\Response(
				response: 403,
				description: "Non autorisé (admin/moderator requis)",
				content: new OA\JsonContent(ref: "#/components/schemas/ErrorResponse")
			)
		]
	)]
	public function index(): AnonymousResourceCollection
	{
		$users = User::query()
			->with('image')
			->orderBy('first_name')
			->orderBy('last_name')
			->get();

		return UserResource::collection($users);
	}

	#[OA\Post(
		path: "/users",
		summary: "Créer un nouvel utilisateur",
		description: "Crée un nouvel utilisateur avec un mot de passe généré automatiquement (envoyé par email)",
		tags: ["Utilisateurs"],
		security: [["bearerAuth" => []]],
		requestBody: new OA\RequestBody(
			required: true,
			content: [
				new OA\MediaType(
					mediaType: "multipart/form-data",
					schema: new OA\Schema(
						required: ["firstName", "lastName", "email", "phone", "role"],
						properties: [
							new OA\Property(
								property: "firstName",
								type: "string",
								maxLength: 255,
								example: "John",
								description: "Prénom de l'utilisateur"
							),
							new OA\Property(
								property: "lastName",
								type: "string",
								maxLength: 255,
								example: "Doe",
								description: "Nom de l'utilisateur"
							),
							new OA\Property(
								property: "email",
								type: "string",
								format: "email",
								example: "john.doe@example.com",
								description: "Adresse email unique"
							),
							new OA\Property(
								property: "phone",
								type: "string",
								maxLength: 255,
								example: "+33123456789",
								description: "Numéro de téléphone unique"
							),
							new OA\Property(
								property: "role",
								type: "string",
								enum: ["admin", "user", "moderator"],
								example: "user",
								description: "Rôle de l'utilisateur"
							),
							new OA\Property(
								property: "image",
								type: "string",
								format: "binary",
								description: "Photo de profil (formats: jpeg, jpg, png, webp, max: 2MB)",
								nullable: true
							)
						]
					)
				)
			]
		),
		responses: [
			new OA\Response(
				response: 201,
				description: "Utilisateur créé avec succès",
				content: new OA\JsonContent(ref: "#/components/schemas/User")
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
				response: 403,
				description: "Non autorisé (admin/moderator requis)",
				content: new OA\JsonContent(ref: "#/components/schemas/ErrorResponse")
			),
			new OA\Response(
				response: 422,
				description: "Erreur de validation",
				content: new OA\JsonContent(ref: "#/components/schemas/ErrorResponse")
			),
			new OA\Response(
				response: 500,
				description: "Erreur lors de l'enregistrement de l'image",
				content: new OA\JsonContent(ref: "#/components/schemas/ErrorResponse")
			)
		]
	)]
	public function store(Request $request): UserResource|JsonResponse
	{
		$request->validate([
			'firstName' => ['required', 'string', 'max:255'],
			'lastName' => ['required', 'string', 'max:255'],
			'email' => ['required', 'email', 'unique:users'],
			'phone' => ['required', 'string', 'unique:users', 'max:255'],
			'role' => ['required', Rule::enum(UserRoleEnum::class)],
			'image' => ['nullable', 'file', 'mimes:jpeg,jpg,png,webp', 'max:2048']
		], [], [
			'firstName' => "Le prénom de l'utilisateur",
			'lastName' => "Le nom de l'utilisateur",
			'email' => "L'adresse mail de l'utilisateur",
			'role' => "Le rôle",
			'phone' => "Le numéro de téléphone de l'utilisateur",
		]);

		$user = User::create([
			...$request->except(['firstName', 'lastName']),
			'first_name' => $request->input('firstName'),
			'last_name' => $request->input('lastName'),
			'password' => Hash::make($password = Str::password(8)),
			'is_active' => true
		]);

		if ($request->hasFile('image')) {
			$path = '';
			try {
				DB::beginTransaction();
				$path = store_file($request, 'image', static::IMAGE_FOLDER);
				$user->image()->create(['path' => $path]);
				DB::commit();
			} catch (Throwable $e) {
				DB::rollBack();
				delete_file($path);
				return _500($e->getMessage());
			}
		}

		try {

			Log::info('Identifiants envoyés par email', [
				'user_id' => $user->id,
				'email' => $user->email
			]);

			$user->notify(new NewUserCredentialsNotification(
				email: $user->email,
				password: $password,
				firstName: $user->first_name,
				lastName: $user->last_name
			));
		} catch (\Throwable $th) {
			Log::error('Échec d\'envoi d\'email des identifiants', [
				'user_id' => $user->id,
				'error' => $th->getMessage()
			]);
		}

		return UserResource::make($user->load('image'));
	}

	#[OA\Put(
		path: "/users/{id}",
		summary: "Mettre à jour un utilisateur",
		description: "Met à jour les informations d'un utilisateur existant",
		tags: ["Utilisateurs"],
		security: [["bearerAuth" => []]],
		parameters: [
			new OA\Parameter(
				name: "id",
				in: "path",
				required: true,
				description: "UUID de l'utilisateur",
				schema: new OA\Schema(
					type: "string",
					format: "uuid",
					example: "550e8400-e29b-41d4-a716-446655440002"
				)
			)
		],
		requestBody: new OA\RequestBody(
			required: true,
			content: new OA\JsonContent(
				required: ["firstName", "lastName", "email", "phone"],
				properties: [
					new OA\Property(
						property: "firstName",
						type: "string",
						maxLength: 255,
						example: "John",
						description: "Prénom de l'utilisateur"
					),
					new OA\Property(
						property: "lastName",
						type: "string",
						maxLength: 255,
						example: "Doe",
						description: "Nom de l'utilisateur"
					),
					new OA\Property(
						property: "email",
						type: "string",
						format: "email",
						example: "john.doe@example.com",
						description: "Nouvelle adresse email"
					),
					new OA\Property(
						property: "phone",
						type: "string",
						maxLength: 255,
						example: "+33123456789",
						description: "Nouveau numéro de téléphone"
					)
				]
			)
		),
		responses: [
			new OA\Response(
				response: 200,
				description: "Utilisateur mis à jour avec succès",
				content: new OA\JsonContent(ref: "#/components/schemas/User")
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
				response: 403,
				description: "Non autorisé",
				content: new OA\JsonContent(ref: "#/components/schemas/ErrorResponse")
			),
			new OA\Response(
				response: 404,
				description: "Utilisateur non trouvé",
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
	public function update(Request $request, string $id): UserResource|JsonResponse
	{
		if (!$user = User::query()->find($id)) {
			return _404();
		}

		$request->validate([
			'firstName' => ['required', 'string', 'max:255'],
			'lastName' => ['required', 'string', 'max:255'],
			'email' => ['required', 'email', Rule::unique(User::class)->ignore($id)],
			// TODO: Modifiable uniquement par l'admin
			// 'role' => ['required', 'string', 'max:255'],
			'phone' => ['required', 'string', Rule::unique(User::class)->ignore($id), 'max:255'],
		]);

		try {
			$user->update([
				...$request->except(['firstName', 'lastName']),
				'first_name' => $request->input('firstName'),
				'last_name' => $request->input('lastName')
			]);

			// if ($request->hasFile('image')) {
			// 	$oldPath = $user->image->path;
			// 	$path = '';
			// 	try {
			// 		DB::beginTransaction();
			// 		$path = store_file($request, 'image', static::IMAGE_FOLDER);
			// 		$user->image->update(['path' => $path]);
			// 		DB::commit();
			// 		delete_file($oldPath);
			// 	} catch (Throwable $th) {
			// 		DB::rollBack();
			// 		delete_file($path);
			// 		return _500($th->getMessage());
			// 	}
			// }
		} catch (Throwable $th) {
			return _500($th->getMessage());
		}

		return new UserResource($user->load('image'));
	}

	#[OA\Delete(
		path: "/users/{id}",
		summary: "Supprimer un utilisateur",
		description: "Supprime un utilisateur s'il n'a pas de réservations associées",
		tags: ["Utilisateurs"],
		security: [["bearerAuth" => []]],
		parameters: [
			new OA\Parameter(
				name: "id",
				in: "path",
				required: true,
				description: "UUID de l'utilisateur",
				schema: new OA\Schema(
					type: "string",
					format: "uuid",
					example: "550e8400-e29b-41d4-a716-446655440002"
				)
			)
		],
		responses: [
			new OA\Response(
				response: 200,
				description: "Utilisateur supprimé avec succès",
				content: new OA\JsonContent(ref: "#/components/schemas/SuccessResponse")
			),
			new OA\Response(
				response: 400,
				description: "L'utilisateur a des réservations et ne peut pas être supprimé",
				content: new OA\JsonContent(ref: "#/components/schemas/ErrorResponse")
			),
			new OA\Response(
				response: 401,
				description: "Non authentifié",
				content: new OA\JsonContent(ref: "#/components/schemas/ErrorResponse")
			),
			new OA\Response(
				response: 403,
				description: "Non autorisé (admin/moderator requis)",
				content: new OA\JsonContent(ref: "#/components/schemas/ErrorResponse")
			),
			new OA\Response(
				response: 404,
				description: "Utilisateur non trouvé",
				content: new OA\JsonContent(ref: "#/components/schemas/ErrorResponse")
			)
		]
	)]
	public function destroy(string $id): JsonResponse
	{
		if (!$user = User::query()->with('image')->find($id)) {
			return _404();
		}

		if ($user->reservations()->count() > 0) {
			return _400('Cet utilisateur a des réservations et ne peut pas être supprimé.');
		}

		delete_file($user->image?->path ?? '');
		$user->image?->delete();

		$user->delete();
		return _200();
	}

	#[OA\Put(
		path: "/users/{id}/toggle-active",
		summary: "Activer/Désactiver un utilisateur",
		description: "Active ou désactive un utilisateur. La désactivation supprime également ses tokens d'authentification.",
		tags: ["Utilisateurs"],
		security: [["bearerAuth" => []]],
		parameters: [
			new OA\Parameter(
				name: "id",
				in: "path",
				required: true,
				description: "UUID de l'utilisateur",
				schema: new OA\Schema(
					type: "string",
					format: "uuid",
					example: "550e8400-e29b-41d4-a716-446655440002"
				)
			)
		],
		requestBody: new OA\RequestBody(
			required: true,
			content: new OA\JsonContent(
				required: ["isActive"],
				properties: [
					new OA\Property(
						property: "isActive",
						type: "boolean",
						example: false,
						description: "Nouveau statut d'activation"
					)
				]
			)
		),
		responses: [
			new OA\Response(
				response: 200,
				description: "Statut d'activation mis à jour avec succès",
				content: new OA\JsonContent(ref: "#/components/schemas/User")
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
				response: 403,
				description: "Non autorisé (admin/moderator requis)",
				content: new OA\JsonContent(ref: "#/components/schemas/ErrorResponse")
			),
			new OA\Response(
				response: 404,
				description: "Utilisateur non trouvé",
				content: new OA\JsonContent(ref: "#/components/schemas/ErrorResponse")
			),
			new OA\Response(
				response: 422,
				description: "Erreur de validation",
				content: new OA\JsonContent(ref: "#/components/schemas/ErrorResponse")
			)
		]
	)]
	public function toggleIsActive(Request $request, string $id): UserResource|JsonResponse
	{
		if (!$user = User::query()->find($id)) {
			return _404();
		}

		$request->validate([
			'isActive' => ['required', 'boolean:strict']
		]);

		$user->update([
			'is_active' => $request->boolean('isActive')
		]);

		if (!$request->boolean('isActive')) {
			$user->tokens()->delete();
		}

		return new UserResource($user->refresh());
	}

	#[OA\Post(
		path: "/users/{id}/reset-password",
		summary: "Réinitialiser le mot de passe d'un utilisateur",
		description: "Génère un nouveau mot de passe aléatoire pour un utilisateur et l'envoie par email",
		tags: ["Utilisateurs"],
		security: [["bearerAuth" => []]],
		parameters: [
			new OA\Parameter(
				name: "id",
				in: "path",
				required: true,
				description: "UUID de l'utilisateur",
				schema: new OA\Schema(
					type: "string",
					format: "uuid",
					example: "550e8400-e29b-41d4-a716-446655440002"
				)
			)
		],
		responses: [
			new OA\Response(
				response: 200,
				description: "Mot de passe réinitialisé avec succès",
				content: new OA\JsonContent(
					properties: [
						new OA\Property(
							property: "message",
							type: "string",
							example: "Mot de passe réinitialisé et envoyé par email"
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
				response: 403,
				description: "Non autorisé (admin/moderator requis)",
				content: new OA\JsonContent(ref: "#/components/schemas/ErrorResponse")
			),
			new OA\Response(
				response: 404,
				description: "Utilisateur non trouvé",
				content: new OA\JsonContent(ref: "#/components/schemas/ErrorResponse")
			)
		]
	)]
	public function resetPassword(string $id): JsonResponse
	{
		if (!$user = User::query()->find($id)) {
			return _404();
		}

		$newPassword = Str::password(10);
		$user->update([
			'password' => Hash::make($newPassword)
		]);

		// TODO: Envoyer le nouveau mot de passe par email
		// Mail::to($user->email)->send(new PasswordResetNotification($newPassword));

		return response()->json([
			'message' => 'Mot de passe réinitialisé avec succès',
			'password_sent_to' => $user->email
		]);
	}
}
