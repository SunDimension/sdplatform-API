<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AccountOpeningBalanceUpdateRequest extends FormRequest
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
            'financial_year_id' => ['required'],
            'financial_period_id' => ['required'],
            'debit' => ['required', 'numeric'],
            'credit' => ['required', 'numeric'],
            'amount' => ['required', 'numeric'],
            'store_id' => ['required', 'string', 'exists:stores,id'],
            'account_no' => ['required', 'string'],
            'account_id' => ['required'],
            'created_by' => ['nullable'],
            'modified_by' => ['nullable'],
            'deleted_by' => ['nullable'],
        ];
    }
}
