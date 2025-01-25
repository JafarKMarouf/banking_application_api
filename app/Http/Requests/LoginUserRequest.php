<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use \Illuminate\Contracts\Validation\Validator;
use App\Http\Response\Response;
use Illuminate\Validation\ValidationException;

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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'identifier' => ['required', 'string'],
            'password' => ['string',  'required', 'min:6', 'max:200'],
        ];
    }

    public function messages()
    {
        return [
            'identifier.required' => 'Please enter your email or phone number.',
            'identifier.string' => 'The identifier must be a string.',
            'password.required' => 'Please enter your password.',
            'password.string' => 'The password must be a string.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $response = Response::validation($validator->errors());
        throw new ValidationException($validator, $response);
    }
}
