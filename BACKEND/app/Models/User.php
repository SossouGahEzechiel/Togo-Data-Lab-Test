<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
	use HasFactory, Notifiable;

	protected $fillable = [
		'name',
		'email',
		'password',
	];

	protected $hidden = [
		'password',
	];

	protected function casts(): array
	{
		return [
			'email_verified_at' => 'datetime',
			'password' => 'hashed',
		];
	}

	// Les réservations émises par l'utilisateur
	public function reservations(): HasMany
	{
		return $this->hasMany(Reservation::class);
	}

	// Les réservations en tant que conducteur
	public function reservationsAsDriver(): HasMany
	{
		return $this->hasMany(Reservation::class, 'driver_id');
	}

	public function images(): MorphOne
	{
		return $this->morphOne(Image::class, "imageable");
	}
}
