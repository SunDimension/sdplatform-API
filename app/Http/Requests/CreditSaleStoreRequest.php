<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreditSaleStoreRequest extends FormRequest
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
            'customer_id' => ['required', 'integer', 'exists:customers,id'],
            'branch_id' => ['required', 'integer', 'exists:branches,id'],
            'warehouse_id' => ['required', 'integer', 'exists:warehouses,id'],
            'product_id' => ['required', 'integer', 'exists:create_items,id'],
            'credit_limit' => ['required','integer','exists:credit_limits,id'],
            'credit_amount' => ['required', 'string'],
            'credit_balance' => ['required', 'string'],
        ];
    }
}
