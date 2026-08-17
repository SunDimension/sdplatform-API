<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MortgageCalculationUpdateRequest extends FormRequest
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
            'user_id' => ['nullable'],
            'property_id' => ['nullable'],
            'loan_amount' => ['required', 'numeric'],
            'interest_rate' => ['required', 'numeric'],
            'loan_term' => ['required', 'integer'],
            'monthly_payment' => ['required', 'numeric'],
            'total_payment' => ['required', 'numeric'],
        ];
    }
}
