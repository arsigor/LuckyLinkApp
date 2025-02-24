<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class RegisterUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'user_name' => 'required|string|max:255',
            'phone_number' => [
                'required',
                'string',
                'max:255',
                'regex:/^\+(\d{1,3})\d{4,14}$/',
                'unique:users,phone_number',
            ],
        ];
    }

    /**
     * Get the custom validation messages.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'phone_number.regex' => 'The phone number must be in international format (e.g., +1234567890).',
        ];
    }
}
