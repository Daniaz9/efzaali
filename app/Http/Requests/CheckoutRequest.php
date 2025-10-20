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
            'dropoff_address' => 'required|string|max:255',
            'dropoff_lat' => 'required|numeric',
            'dropoff_long' => 'required|numeric',
            'description' => 'nullable|string|max:500',
        ];
    }
}
