<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InvoiceStoreRequest extends FormRequest
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
            'warehouse_id' => ['required', 'integer', 'exists:warehouses,id'],
            'customer_id' => ['required', 'integer', 'exists:customers,id'],
            'invoice_number' => ['required', 'string'],
            'order_number' => ['required', 'string'],
            'invoice_date' => ['required'],
            'item_id' => ['required', 'integer', 'exists:items,id'],
            'rate' => ['required', 'string'],
            'quantity' => ['required', 'string'],
            'discount_id' => ['required', 'integer', 'exists:discounts,id'],
            'tax_id' => ['required', 'integer', 'exists:taxes,id'],
            'amount' => ['required', 'string'],
        ];
    }
}
