<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FinancialQuarterUpdateRequest extends FormRequest
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
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string'],
            'date_from' => ['required', 'date'],
            'date_to' => ['required', 'date'],
            'is_active' => ['required'],
            'financial_year_id' => ['required'],
            'created_by' => ['nullable'],
            'modified_by' => ['nullable'],
            'deleted_by' => ['nullable'],
        ];
    }
}
