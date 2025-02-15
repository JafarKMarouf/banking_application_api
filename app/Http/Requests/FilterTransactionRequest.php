<?php

namespace App\Http\Requests;

use App\Enums\TransactionCategoryEnum;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FilterTransactionRequest extends FormRequest
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
            'category' => [
                'nullable',
                'string',
                Rule::in([
                    TransactionCategoryEnum::WITHDRAW->value,
                    TransactionCategoryEnum::DEPOSIT->value
                ]),
            ],
            'start_date' => [
                Rule::requiredIf(request()->query('end_date') != null),
                'date_format:Y-m-d'
            ],
            'end_date' => [
                Rule::requiredIf(request()->query('start_date') != null),
                'date_format:Y-m-d'
            ],
        ];
    }
}
