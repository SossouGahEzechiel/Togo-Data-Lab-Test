<?php

namespace App\Models;

use App\Enums\ReservationStatusEnum;
use App\Enums\VehicleStatusEnum;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Vehicle extends Model
{
	use HasUuids;

	protected $fillable = [
		'model',
		'brand',
		'type',
		'registration_number',
		'registration_date',
		'seats_count',
		'status',
		'reason',
		'images',
	];

	public $timestamps = false;

	protected function casts()
	{
		return [
			'registration_date' => 'datetime:Y-m-d',
			'seats_count' => 'integer',
			'status' => VehicleStatusEnum::class
		];
	}

	public function reservations(): HasMany
	{
		return $this->hasMany(Reservation::class);
	}

	public function images(): MorphMany
	{
		return $this->morphMany(Image::class, "imageable");
	}

	public function isAvailable(Carbon $from, ?Carbon $to = null): bool
	{
		$to = $to ?? $from;

		return $this->reservations()
			->whereNotIn('status', [ReservationStatusEnum::PASSED->value])
			->where(function (Builder $query) use ($from, $to) {
				$query->whereBetween('from', [$from, $to])
					->orWhereBetween('to', [$from, $to])
					->orWhere(function (Builder $q) use ($from, $to) {
						$q->where('from', '<=', $from)
							->where('to', '>=', $to);
					});
			})
			->whereIn('status', [
				// ReservationStatusEnum::PENDING->value,
				ReservationStatusEnum::VALIDATED->value
			])
			->doesntExist();
	}

	public function otherReservationExcept(string $reservationId, Carbon $from, Carbon $to): HasMany
	{
		return $this->reservations()
			->where('status', ReservationStatusEnum::PENDING)
			->whereNot('id', $reservationId)
			->whereDate('from', '<=', $to)
			->whereDate('to', '>=', $from);
	}
}
