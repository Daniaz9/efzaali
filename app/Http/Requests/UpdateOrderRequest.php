<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'customer_id' => 'required|exists:users,id',
            'driver_id' => 'required|exists:users,id',
            'type' => 'required|',
            'delivery_type' => 'required|',
            'status' => 'required|',
            'pickup_address' => 'required|',
            'dropoff_address' => 'required|',
            'pickup_lat' => 'required|',
            'pickup_long' => 'required|',
            'dropoff_lat' => 'required|',
            'dropoff_long' => 'required|',
            'distance' => 'required|',
            'delivery_fee' => 'required|',
            'total_price' => 'required|',
            'description' => 'required|',
            'driver_assigned_at' => 'required|',
            'preparing_at' => 'required|',
            'picked_up_at' => 'required|',
            'on_the_way_at' => 'required|',
            'delivered_at' => 'required|',
            'cancelled_at' => 'required|',
        ];
    }
}
