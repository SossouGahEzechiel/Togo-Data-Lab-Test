<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
	use HasApiTokens, HasUuids;

	protected $fillable = [
		'first_name',
		'last_name',
		'email',
		'role',
		'phone',
		'password',
		'password_reset_at',
		'is_active'
	];

	public $timestamps = false;

	protected $hidden = [
		'password',
	];

	protected function casts(): array
	{
		return [
			'email_verified_at' => 'datetime',
			'password' => 'hashed',
			'is_active' => 'boolean'
		];
	}

	// Les réservations émises par l'utilisateur
	public function reservations(): HasMany
	{
		return $this->hasMany(Reservation::class);
	}

	// Les réservations en tant que conducteur
	public function reservationsDriver(): HasMany
	{
		return $this->hasMany(Reservation::class, 'driver_id');
	}

	public function image(): MorphOne
	{
		return $this->morphOne(Image::class, "imageable");
	}
}
