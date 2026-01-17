<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
	public function toArray(Request $request): array
	{
		return [
			'id' => $this->id,
			'firstName' => $this->first_name,
			'lastName' => $this->last_name,
			'email' => $this->email,
			'role' => $this->role,
			'phone' => $this->phone,
			'isActive' => $this->is_active,
			'image' => new ImageResource($this->whenLoaded('image')),
			'token' => $this->whenHas('token')
		];
	}
}
