<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreditTransactionUpdateRequest extends FormRequest
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
            'customer_id' => ['required', 'integer', 'exists:customers,id'],
            'sales_order_id' => ['nullable', 'integer', 'exists:sales_orders,id'],
            'sales_receipt_id' => ['nullable', 'integer', 'exists:sales_receipts,id'],
            'amount' => ['required', 'string'],
            'credit_limit' => ['nullable', 'string'],
            'credit_balance_before' => ['nullable', 'string'],
            'type' => ['required', "in:('credit',"],
            'created_by' => ['required'],
            'modified_by' => ['nullable'],
            'deleted_by' => ['nullable'],
        ];
    }
}
