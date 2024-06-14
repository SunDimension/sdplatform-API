<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SalesReceiptStoreRequest extends FormRequest
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
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'tax_id' => ['required', 'integer', 'exists:taxes,id'],
            'payment_mode_id' => ['required', 'integer', 'exists:payment_modes,id'],
            'discount_id' => ['required', 'integer', 'exists:discounts,id'],
            'quantity' => ['required', 'string'],
            'rate' => ['required', 'string'],
            'amount' => ['required', 'string'],
            'receipt_date' => ['required'],
            'customer_note' => ['required', 'string'],
        ];
    }
}
