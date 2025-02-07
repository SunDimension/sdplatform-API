<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CashierRemittanceStoreRequest extends FormRequest
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
            'cash_discrepancy_id' => ['required', 'integer', 'exists:cash_discrepancies,id'],
            'branch_id' => ['required', 'integer', 'exists:branches,id'],
            'store_id' => ['required', 'integer', 'exists:stores,id'],
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'approved_by' => ['nullable'],
            'amount' => ['required'],
            'date' => ['required', 'date'],
            'approval_date' => ['nullable'],
            'discrepancy_amount' => ['nullable'],

           
        ];
    }
}
