<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class RegisterUserRequest extends FormRequest
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
            'name' => ['string', 'required', 'min:3', 'max:200'],
            'email' => ['string', 'required', 'email', 'unique:users,email', 'max:200'],
            'phone_number' => ['string', 'required', 'unique:users,phone_number', 'min:10', 'max:20', 'regex:/^([0-9\s\-\+\(\)]*)$/'],
            'password' => ['string', 'required', 'min:6', 'max:200'],
        ];
    }
    public function messages(): array
    {
        return [
            'name.required' => 'The name field is required.',
            'name.string' => 'The name must be a valid string.',
            'name.min' => 'The name must be at least 3 characters.',
            'name.max' => 'The name may not be greater than 200 characters.',

            'email.required' => 'The email field is required.',
            'email.string' => 'The email must be a valid string.',
            'email.email' => 'The email must be a valid email address.',
            'email.unique' => 'The email has already been taken.',
            'email.max' => 'The email may not be greater than 200 characters.',

            'phone_number.required' => 'The phone number field is required.',
            'phone_number.string' => 'The phone number must be a valid string.',
            'phone_number.unique' => 'The phone number has already been taken.',
            'phone_number.min' => 'The phone number must be at least 10 characters.',
            'phone_number.max' => 'The phone number may not be greater than 20 characters.',
            'phone_number.regex' => 'The phone number format is invalid.',

            'password.required' => 'The password field is required.',
            'password.string' => 'The password must be a valid string.',
            'password.min' => 'The password must be at least 6 characters.',
            'password.max' => 'The password may not be greater than 200 characters.',
        ];
    }
}
