<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Mission extends Model
{
	use HasUuids;

	private const WITH_PIVOT_COLUMNS = [
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

	private const PIVOT_TABLE_ALIAS = 'reservations';

	private const PIVOT_ATTRIBUT_ALIAS = 'reservation';

	protected $fillable = [
		'label',
		'description',
		'from',
		'to'
	];

	public $timestamps = false;

	protected function casts()
	{
		return [
			'from' => 'date:Y-m-d',
			'to' => 'date:Y-m-d',
		];
	}

	public function reservations()
	{
		return $this->hasMany(Reservation::class);
	}

	public function vehicles(): BelongsToMany
	{
		return $this->belongsToMany(Vehicle::class, static::PIVOT_TABLE_ALIAS)
			->using(Reservation::class)
			->as(static::PIVOT_ATTRIBUT_ALIAS)
			->withPivot(static::WITH_PIVOT_COLUMNS);
	}

	public function users(): BelongsToMany
	{
		return $this->belongsToMany(User::class, static::PIVOT_TABLE_ALIAS)
			->using(Reservation::class)
			->as(static::PIVOT_ATTRIBUT_ALIAS)
			->withPivot(static::WITH_PIVOT_COLUMNS);
	}

	public function drivers(): BelongsToMany
	{
		return $this->belongsToMany(User::class, static::PIVOT_TABLE_ALIAS, foreignPivotKey: 'driver_id')
			->using(Reservation::class)
			->as(static::PIVOT_ATTRIBUT_ALIAS)
			->withPivot(static::WITH_PIVOT_COLUMNS);
	}
}
