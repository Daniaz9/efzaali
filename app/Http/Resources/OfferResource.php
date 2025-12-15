<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OfferResource extends JsonResource
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
            'order' => new OrderResource($this->whenLoaded('order')),
            'driver' => new UserResource($this->whenLoaded('driver')),
            'price' => $this->price,
            'message' => $this->message,
            'average_delivery_time'=> $this->average_delivery_time,
        ];
    }}
