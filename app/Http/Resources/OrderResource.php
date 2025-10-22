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
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'customer' => new UserResource($this->whenLoaded('customer')),
            'driver' => new UserResource($this->whenLoaded('driver')),
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

            // Combine all products into "items"
            'items' => $this->whenLoaded('products', function () {
                return $this->products->map(function ($product) {
                    return [
                        'id' => $product->id,
                        'name' => $product->name,
                        'price' => $product->price,
                        'description' => $product->description,
                        'quantity' => $product->pivot->quantity,
                        'total_price' => $product->pivot->total_price,
                        'store' => new StoreResource($product->store),
                    ];
                });
            }),
        ];
    }
}
