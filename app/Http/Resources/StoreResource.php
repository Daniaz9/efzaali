<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StoreResource extends JsonResource
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
            'description' => $this->description,
            'is_active' => $this->is_active,
            'photo' => $this->photo ? [
                'path' => $this->photo->path,
                'small_path' => $this->photo->small_path,
            ] : null,
            'address' => $this->address,
            'lat' => $this->lat ?? null,
            'long' => $this->long ?? null,

        ];
    }
}
