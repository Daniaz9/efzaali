<?php

namespace App\Http\Requests;

use App\Models\Role;
use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'rating' => 'nullable',
//            'role' => ['required', 'string', Role::in(['customer','driver','admin'])],
            'phone_number' => 'required|string|max:20',
            'is_available' => 'sometimes|boolean',
            'avatar' => 'sometimes|image|mimes:jpg,png,jpeg,gif,webp|max:2048',
        ];
    }
}
