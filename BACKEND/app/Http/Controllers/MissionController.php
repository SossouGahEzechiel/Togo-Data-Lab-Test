<?php

namespace App\Http\Controllers;

use App\Http\Resources\MissionResource;
use App\Models\Mission;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class MissionController extends Controller
{
	public function index(): AnonymousResourceCollection
	{
		$missions = Mission::query()
			// ->with(['vehicles', 'users', 'drivers'])
			->orderBy('from')
			->orderBy('label')
			->get();

		return MissionResource::collection($missions);
	}

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

	public function show(string $id): MissionResource|JsonResponse
	{
		if (!$mission = Mission::query()->with(['vehicles', 'users', 'drivers'])->find($id)) {
			return _404();
		}
		return new MissionResource($mission);
	}

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
