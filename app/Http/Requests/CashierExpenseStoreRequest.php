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
            'branch_id' => ['required', 'integer', 'exists:branches,id'],
            'expense_line_id' => ['required', 'integer', 'exists:expense_lines,id'],
            'store_id' => ['required', 'integer', 'exists:stores,id'],
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'amount' => ['required'],
            'narration'=>['nullable'],
            'date' => ['required', 'date'],
            'approved_by' => ['nullable'],
            'approval_date' => ['nullable'],
            'status' => ['nullable', 'string'],
           
        ];
    }
}
