<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone_number' => $this->phone_number,
            'photo' => $this->photo ? [
                'path' => $this->photo->path,
                'small_path' => $this->photo->small_path,
            ] : null,
            'roles' => $this->roles->pluck('name'),
            'rating' => $this->ratingStats()['avg'],
        ];
    }
}
