<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
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
            'price' => $this->price,
            'description' => $this->description,
            'store' => $this->whenLoaded('store', function () {
                return [
                    'id' => $this->store->id,
                    'name' => $this->store->name,
                    'photo' => $this->store->photo ? [
                        'path' => $this->store->photo->path,
                        'small_path' => $this->store->photo->small_path,
                    ] : null,
                ];
            }),
            'photos' => $this->whenLoaded('photos', function () {
                return $this->photos->map(fn($photo) => [
                    'path' => $photo->path,
                    'small_path' => $photo->small_path,
                ]);
            }),
        ];
    }}
