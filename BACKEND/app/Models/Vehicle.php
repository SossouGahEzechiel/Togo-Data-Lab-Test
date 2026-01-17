<?php

namespace App\Models;

use App\Enums\VehicleStatusEnum;
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

	public function reservations() : HasMany {
		return $this->hasMany(Reservation::class);
	}

	public function images(): MorphMany {
		return $this->morphMany(Image::class, "imageable");
	}
}
