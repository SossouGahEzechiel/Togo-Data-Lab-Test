<?php

namespace App\Http\Controllers;

use App\Enums\UserRoleEnum;
use App\Http\Resources\ImageResource;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Throwable;

class UserController extends Controller
{

	private const IMAGE_FOLDER = 'users';

	public function index(): AnonymousResourceCollection
	{
		$users = User::query()
			->with('image')
			->orderBy('first_name')
			->orderBy('last_name')
			->get();

		return UserResource::collection($users);
	}

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

		// TODO: Envoyer le mot de passe par email

		return UserResource::make($user->load('image'));
	}

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

	// public function updateImage(Request $request): ImageResource|JsonResponse
	// {
	// 	dd($request->hasFile('image'));
	// 	$request->validate([
	// 		'image' => ['required', 'file', 'mimes:jpeg,jpg,png,webp'],
	// 	]);

	// 	$user = $request->user();

	// 	if ($request->hasFile('image')) {
	// 		$oldPath = $user->image->path;
	// 		$path = '';
	// 		try {
	// 			DB::beginTransaction();
	// 			$path = store_file($request, 'image', static::IMAGE_FOLDER);
	// 			$user->image->update(['path' => $path]);
	// 			DB::commit();
	// 			delete_file($oldPath);
	// 		} catch (Throwable $th) {
	// 			DB::rollBack();
	// 			delete_file($path);
	// 			return _500($th->getMessage());
	// 		}
	// 	} else {
	// 		if ($user->image) {
	// 			delete_file($user->image->path ?? '');
	// 			$user->image->delete();
	// 		}
	// 	}

	// 	// TODO: Implémenter la logique de mise à jour de l'image
	// 	return _500();
	// }
}
