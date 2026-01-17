<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthController extends Controller
{
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
