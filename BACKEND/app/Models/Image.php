<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Image extends Model
{
	use HasUuids;

	protected $fillable = [
		'path',
		'imageable_id',
		'imageable_type',
	];

	// public $timestamps = false;

	public function imageable() : MorphTo {
		return $this->morphTo();
	}
}
