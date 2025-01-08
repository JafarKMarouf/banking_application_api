<?php

namespace App\Http\Requests;

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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
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
}
