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
use Throwable;

class VehicleController extends Controller
{
	private const FOLDER_NAME = 'vehicles';

	public function index(): AnonymousResourceCollection
	{
		$vehicles = Vehicle::query()->orderBy('registration_number')->get();
		return VehicleResource::collection($vehicles);
	}

	public function store(Request $request): VehicleResource|JsonResponse
	{
		$request->validate([
			'brand' => ['required', 'min:4', 'max:31'],
			'model' => ['required', 'min:4', 'max:31'],
			'type' => ['required', Rule::enum(VehicleTypeEnum::class)],
			'registrationNumber' => ['required', 'min:8', 'max:31'],
			'registrationDate' => ['required', 'date'],
			'seatsCount' => ['required', 'numeric', 'min:1', 'max:15'],
			'status' => ['required', Rule::enum(VehicleStatusEnum::class)],
			'reason' => ['nullable', 'max:1023'],
			'images.*' => ['nullable', 'file', '.jpeg,.jpg,.jpeg']
		], [], [
			'brand' => "La marque du véhicule",
			'model' => "Le modèle du véhicule",
			'type' => "Le type du véhicule",
			'registrationNumber' => "L'immatriculation",
			'registrationDate' => "La date d'enregistrement",
			'seatsCount' => "Le nombre de sièges",
			'status' => "Le statut du véhicule",
			'reason' => "La raison du statut",
			'images.*' => "L'image du véhicule",
		]);

		$vehicle = Vehicle::query()->create([
			...$request->except(['registrationNumber', 'registrationDate', 'seatsCount', 'images']),
			'registration_number' => $request->input('registrationNumber'),
			'registration_date' => $request->date('registrationDate'),
			'seats_count' => $request->integer('seatsCount')
		]);


		$request->collect('images')->each(function(string $fileKey) use ($request, $vehicle) {
			$fileName = '';

			try {
				DB::beginTransaction();
				$fileName = store_file($request, $fileKey, static::FOLDER_NAME);
				$vehicle->images()->create(['path' => $fileName]);
				DB::commit();
				$fileName = '';
			} catch (Throwable) {
				DB::rollBack();
				delete_file($fileName);
			}
		});

		return new VehicleResource($vehicle->load('images'));
	}

	public function get(string $id): VehicleResource|JsonResponse
	{
		if (!$vehicle = Vehicle::query()->find($id)) {
			return _404();
		}

		return new VehicleResource($vehicle);
	}

	public function update(Request $request, string $id): VehicleResource|JsonResponse
	{
		$request->validate([
			'brand' => ['required', 'min:4', 'max:31'],
			'model' => ['required', 'min:4', 'max:31'],
			'type' => ['required', Rule::enum(VehicleTypeEnum::class)],
			'registrationNumber' => ['required', 'min:8', 'max:31'],
			'registrationDate' => ['required', 'date'],
			'seatsCount' => ['required', 'numeric', 'integer', 'min:1', 'max:15'],
			'status' => ['required', Rule::enum(VehicleStatusEnum::class)],
			'reason' => ['nullable', 'max:1023'],
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

		if (!$vehicle = Vehicle::query()->find($id)) {
			return _404();
		}

		$vehicle->update([
			...$request->except(['registrationNumber', 'registrationDate', 'seatsCount', 'images']),
			'registration_number' => $request->input('registrationNumber'),
			'registration_date' => $request->date('registrationDate'),
			'seats_count' => $request->integer('seatsCount')
		]);
		return new VehicleResource($vehicle);
	}

	public function delete(string $id): JsonResponse
	{
		if (!$vehicle = Vehicle::query()->find($id)) {
			return _404();
		}

		if ($vehicle->reservations()->count() > 0) {
			return _400();
		}

		try {
			$vehicle->images->each(function (Vehicle $vehicle) {
				$vehicle->images->each(function (Image $image) {
					// TODO: Mettre en place le suivi des transaction
					delete_file($image->path);
				});
				$vehicle->images()->delete();
			});

			$vehicle->delete();
		} catch (Throwable $th) {
			return _500($th->getMessage());
		}
		return _200();
	}
}
