<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class LoginUserRequest extends FormRequest
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
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'identifier' => ['required', 'string'],
            'password' => ['string',  'required', 'min:6', 'max:200'],
        ];
    }

    public function messages(): array
    {
        return [
            'identifier.required' => 'Please enter your email or phone number.',
            'identifier.string' => 'The identifier must be a string.',
            'password.required' => 'Please enter your password.',
            'password.string' => 'The password must be a string.',
        ];
    }
}
