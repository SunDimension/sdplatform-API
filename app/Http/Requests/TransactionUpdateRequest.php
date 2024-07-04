<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TransactionUpdateRequest extends FormRequest
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
            'financial_period_id' => ['required'],
            'transaction_date' => ['required', 'date'],
            'transcode' => ['required', 'string'],
            'transtype' => ['required', 'string'],
            'naration' => ['required', 'string'],
            'debit' => ['required', 'numeric'],
            'credit' => ['required', 'numeric'],
            'amount' => ['required', 'numeric'],
            'warehouse_id' => ['required', 'integer', 'exists:warehouses,id'],
            'account_no' => ['required', 'string'],
            'account_id' => ['required'],
            'created_by' => ['nullable'],
            'modified_by' => ['nullable'],
            'deleted_by' => ['nullable'],
        ];
    }
}
