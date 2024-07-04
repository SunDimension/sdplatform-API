<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PeriodAccountDailyStoreRequest extends FormRequest
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
            'period_date' => ['required', 'date'],
            'debit' => ['required', 'numeric'],
            'credit' => ['required', 'numeric'],
            'amount' => ['required', 'numeric'],
            'warehouse_id' => ['required', 'integer', 'exists:warehouses,id'],
            'account_no' => ['required', 'string'],
            'account_id' => ['required', 'integer', 'exists:accounts,id'],
            'created_by' => ['required'],
            'modified_by' => ['required'],
            'deleted_by' => ['required'],
        ];
    }
}
