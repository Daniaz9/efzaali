<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CheckoutRequest extends FormRequest
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
            'delivery_type' => 'required|in:store,custom',
            'use_saved_location' => 'boolean',
            'dropoff_address' => 'required_if:use_saved_location,false|string',
            'dropoff_lat' => 'required_if:use_saved_location,false|numeric',
            'dropoff_long' => 'required_if:use_saved_location,false|numeric',
            'pickup_address' => 'required_if:delivery_type,custom|string',
            'pickup_lat' => 'required_if:delivery_type,custom|numeric',
            'pickup_long' => 'required_if:delivery_type,custom|numeric',
            'description' => 'sometimes|string'
        ];
    }
}
