<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use OpenApi\Attributes as OA;

class AuthController extends Controller
{
	#[OA\Post(
		path: "/auth/login",
		summary: "Connexion utilisateur",
		description: "Authentifie un utilisateur et retourne un token d'accès",
		tags: ["Authentification"],
		requestBody: new OA\RequestBody(
			required: true,
			content: new OA\JsonContent(
				required: ["email", "password"],
				properties: [
					new OA\Property(
						property: "email",
						type: "string",
						format: "email",
						example: "user@example.com"
					),
					new OA\Property(
						property: "password",
						type: "string",
						format: "password",
						example: "password123"
					)
				]
			)
		),
		responses: [
			new OA\Response(
				response: 200,
				description: "Connexion réussie",
				content: new OA\JsonContent(ref: "#/components/schemas/UserWithToken")
			),
			new OA\Response(
				response: 400,
				description: "Identifiants invalides",
				content: new OA\JsonContent(ref: "#/components/schemas/ErrorResponse")
			),
			new OA\Response(
				response: 422,
				description: "Erreur de validation",
				content: new OA\JsonContent(ref: "#/components/schemas/ErrorResponse")
			)
		]
	)]
	public function login(Request $request): UserResource|JsonResponse
	{
		$request->validate([
			'email' => ['required', 'email'],
			'password' => ['required']
		]);

		if (!$user = User::query()->firstWhere('email', $request->input('email'))) {
			return _400("Les identifiants de connexion que vous avez fournis sont invalides");
		}

		if (!Hash::check($request->input('password'), $user->password)) {
			return _400("Les identifiants de connexion que vous avez fournis sont invalides");
		}

		if (!$user->is_active) {
			return _403("Votre compte n'est pas actif");
		}

		return new UserResource($this->generateToken($user));
	}

	#[OA\Post(
		path: "/auth/logout",
		summary: "Déconnexion utilisateur",
		description: "Invalide le token d'authentification de l'utilisateur courant",
		tags: ["Authentification"],
		security: [["bearerAuth" => []]],
		responses: [
			new OA\Response(
				response: 200,
				description: "Déconnexion réussie",
				content: new OA\JsonContent(ref: "#/components/schemas/SuccessResponse")
			),
			new OA\Response(
				response: 401,
				description: "Non authentifié",
				content: new OA\JsonContent(ref: "#/components/schemas/ErrorResponse")
			)
		]
	)]
	public function logout(): JsonResponse
	{
		$user = request()->user();

		$tokenId = $this->getAuthTokenId();

		$user->tokens()->find($tokenId)->delete();
		return _200();
	}

	#[OA\Put(
		path: "/auth/configure-password",
		summary: "Configurer le mot de passe utilisateur",
		description: "Met à jour le mot de passe de l'utilisateur authentifié avec des règles de sécurité strictes",
		tags: ["Authentification"],
		security: [["bearerAuth" => []]],
		requestBody: new OA\RequestBody(
			required: true,
			content: new OA\JsonContent(
				required: ["password", "password_confirmation"],
				properties: [
					new OA\Property(
						property: "password",
						type: "string",
						format: "password",
						minLength: 8,
						example: "Str0ngP@ssw0rd!",
						description: "Nouveau mot de passe (minimum 8 caractères, lettres majuscules/minuscules, chiffres et symboles)"
					),
					new OA\Property(
						property: "password_confirmation",
						type: "string",
						format: "password",
						example: "Str0ngP@ssw0rd!",
						description: "Confirmation du mot de passe"
					)
				]
			)
		),
		responses: [
			new OA\Response(
				response: 200,
				description: "Mot de passe mis à jour avec succès",
				content: new OA\JsonContent(ref: "#/components/schemas/User")
			),
			new OA\Response(
				response: 400,
				description: "Données invalides ou mot de passe faible",
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
				content: new OA\JsonContent(
					properties: [
						new OA\Property(
							property: "message",
							type: "string",
							example: "The given data was invalid."
						),
						new OA\Property(
							property: "errors",
							type: "object",
							properties: [
								new OA\Property(
									property: "password",
									type: "array",
									items: new OA\Items(
										type: "string",
										example: [
											"The password must be at least 8 characters.",
											"The password must contain at least one uppercase and one lowercase letter.",
											"The password must contain at least one symbol.",
											"The password must contain at least one number.",
											"The password confirmation does not match.",
											"The given password has appeared in a data leak. Please choose a different password."
										]
									)
								)
							]
						)
					]
				)
			)
		]
	)]
	public function configurePassword(Request $request): JsonResponse|UserResource
	{
		$request->validate([
			'password' => [
				'required',
				'confirmed',
				Password::min(8)
					->letters()
					->mixedCase()
					->numbers()
					->symbols()
					->uncompromised()
			]
		], [], [
			'password' => "Le mot de passe",
		]);

		$user = $request->user();
		$user->update([
			'password' => $request->input('password'),
			'has_configured_password' => now()
		]);

		return new UserResource($user->refresh());
	}

	private function generateToken(User $user): User
	{
		$user->token = $user->createToken('auth-token')->plainTextToken;
		return $user;
	}

	private function getAuthTokenId(): string
	{
		return Str::between(request()->header('Authorization'), ' ', '|');
	}
}
