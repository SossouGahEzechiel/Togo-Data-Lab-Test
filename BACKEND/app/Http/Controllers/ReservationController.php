<?php

namespace App\Http\Controllers;

use App\Enums\ReservationStatusEnum;
use App\Http\Resources\ReservationResource;
use App\Models\Reservation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Validation\Rule;

class ReservationController extends Controller
{
	public function index(): AnonymousResourceCollection
	{

		$reservations = Reservation::query()
			->orderBy('from')
			->orderBy('to')
			->get();

		return ReservationResource::collection($reservations);
	}

	public function store(Request $request): ReservationResource|JsonResponse
	{
		$request->validate([
			'from' => ['required', 'date'],
			'to' => ['nullable', 'date'],
			'missionId' => ['required', 'exists:missions,id'],
			'vehicleId' => ['required', 'exists:vehicles,id'],
			'driverId' => ['nullable', 'exists:users,id'],
		], [], []);

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

	public function show(string $id): ReservationResource|JsonResponse
	{
		if (!$reservation = Reservation::query()->find($id)) {
			return _404();
		}

		return new ReservationResource($reservation);
	}

	public function assignDriver(Request $request, string $id): ReservationResource|JsonResponse
	{

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
			'status' => ['nullable', Rule::enum(ReservationStatusEnum::class)]
		], [], []);

		$reservation->update([
			...$request->only(['from', 'to']),
			'mission_id' => $request->input('missionId'),
			'vehicle_id' => $request->input('vehicleId'),
			'driver_id' => $request->input('driverId'),
			'user_id' => auth()->id(),
			'status' => $request->enum('status', ReservationStatusEnum::class, $reservation->status)
		]);
		return new ReservationResource($reservation);
	}

	public function destroy(string $id): JsonResponse
	{
		if (!$reservation = Reservation::query()->find($id)) {
			return _404();
		}

		$reservation->delete();

		return _200();
	}
}
