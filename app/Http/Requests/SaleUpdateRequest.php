<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SaleUpdateRequest extends FormRequest
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
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'branch_id' => ['required', 'integer', 'exists:branches,id'],
            'warehouse_id' => ['required', 'integer', 'exists:warehouses,id'],
            'quantity' => ['required', 'numeric'],
            'price' => ['required', 'numeric'],
            'discount_id' => ['required', 'integer', 'exists:discounts,id'],
            'discount' => ['required', 'numeric'],
            'sales_order_number' => ['required', 'string'],
            'total_amount' => ['required', 'string'],
            'amount_paid' => ['required', 'numeric'],
            'balance_amount' => ['required', 'numeric'],
            'payment_mode' => ['required'],
        ];
    }
}
