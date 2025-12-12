<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CashierExpenseStoreRequest extends FormRequest
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
            'branch_id' => ['required', 'string', 'exists:branches,id'],
            'account_id' => ['required', 'string', 'exists:ledger_accounts,account_id'],
            'store_id' => ['required', 'string', 'exists:stores,id'],
            'user_id' => ['required', 'string', 'exists:users,id'],
            'amount' => ['required'],
            'payment_method' => 'required|string|in:Cash,Bank',
            'narration' => ['nullable'],
            'date' => ['required', 'date'],
            'approved_by' => ['nullable'],
            'approval_date' => ['nullable'],
            'status' => ['nullable', 'string'],

        ];
    }
}
