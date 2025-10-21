<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
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
            'customer' => new UserResource($this->customer),
            'type' => $this->type,
            'delivery_type' => $this->delivery_type,
            'status' => $this->status,
            'pickup_address' => $this->pickup_address,
            'dropoff_address' => $this->dropoff_address,
            'pickup_lat' => $this->pickup_lat,
            'pickup_long' => $this->pickup_long,
            'dropoff_lat' => $this->dropoff_lat,
            'dropoff_long' => $this->dropoff_long,
            'delivery_fee' => $this->delivery_fee,
            'total_price' => $this->total_price,
            'description' => $this->description,
            'products' => ProductResource::collection($this->whenLoaded('products')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];    }
}
