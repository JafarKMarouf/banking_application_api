<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class SetupPinRequest extends FormRequest
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
            'pin' => 'required|string|min:4|max:4'
        ];
    }
    public function messages(): array
    {
        return [
            'pin.required' => 'The pin is required.',
            'pin.min' => 'The pin must be at least 4 characters.',
            'pin.max' => 'The pin may not be greater than 4 characters.',
        ];
    }
}
