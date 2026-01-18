<?php

namespace App\Models;

use App\Enums\ReservationStatusEnum;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class Reservation extends Pivot
{
	use HasUuids;

	protected $table = 'reservations';

	protected $fillable = [
		'from',
		'to',
		'condition',
		'status',
		'return_date',
		'mission_id',
		'user_id',
		'driver_id',
		'vehicle_id',
	];

	public $timestamps = false;

	public static function with($relations)
	{
		return [
			'author',
			'mission',
			'driver',
			'vehicle'
		];
	}

	protected function casts()
	{
		return [
			'from' => 'date',
			'to' => 'date',
			'status' => ReservationStatusEnum::class
		];
	}

	public function mission(): BelongsTo
	{
		return $this->belongsTo(Mission::class);
	}

	public function driver(): BelongsTo
	{
		return $this->belongsTo(User::class, 'driver_id');
	}

	public function vehicle(): BelongsTo
	{
		return $this->belongsTo(Vehicle::class);
	}

	public function author(): BelongsTo
	{
		return $this->belongsTo(User::class, 'user_id');
	}
}
