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
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            
            'sales_order_id' => ['required'],
            'branch_id' => ['required', 'integer','exists:branches,id'],
            'sales_invoice' => ['nullable', 'integer','exists:sales_invoice,id'],
            'payment_type' => ['required'],
            'customer_id' => ['required', 'integer','exists:customers,id'],
            'store_id' => ['required', 'integer','exists:stores,id'],
            'user_id' => ['required', 'integer','exists:users,id'],
            'cashier_id' => ['required', 'integer','exists:users,id'],
            'sales_receipt_number' => ['string', 'unique:sales_receipts'],
            'total_amount' => ['required'],
            'amount_paid' => ['required'],
            'receipt_date' => ['string'],
            'payment_detail'=>['array'],
            'payment_detail.*.amount'=>['required'],
            'payment_detail.*.payment_type'=>['required','string']
        ];
    }
}
