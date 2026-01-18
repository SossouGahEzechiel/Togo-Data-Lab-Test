<?php

namespace App\Http\Controllers;

use App\Enums\ReservationStatusEnum;
use App\Enums\VehicleStatusEnum;
use App\Http\Resources\ReservationResource;
use App\Models\Reservation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Throwable;

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

	public function destroy(string $id): JsonResponse
	{
		if (!$reservation = Reservation::query()->find($id)) {
			return _404();
		}

		$reservation->delete();

		return _200();
	}
}
