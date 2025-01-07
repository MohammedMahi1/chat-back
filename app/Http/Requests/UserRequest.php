<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Allow validation
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'username' => [
                'required',
                'unique:users',
                'regex:/^(?=.*[0-9])(?=.*[A-Za-z])[A-Za-z0-9]{8,12}$/', // At least one letter, one number, length 8-12
            ],
            'password' => [
                'required',
                'regex:/^(?=.*[0-9])(?=.*[A-Za-z])[A-Za-z0-9-]{6,12}$/',
            ],
        ];
    }

    /**
     * Custom error messages for validation.
     */
    public function messages(): array
    {
        return [
            'username.regex' => 'The username must contain at least one letter, one number, and be between 8 and 12 characters long.',
            'password.regex' => 'The password must contain at least one letter, one number, and be between 6 and 12 characters long. The character "-" is optional.',
        ];
    }
}
