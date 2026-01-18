<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
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
