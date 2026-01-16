<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Vehicle extends Model
{
	use HasUuids;

	public function reservations() : HasMany {
		return $this->hasMany(Reservation::class);
	}

	public function images(): MorphMany {
		return $this->morphMany(Image::class, "imageable");
	}
}
