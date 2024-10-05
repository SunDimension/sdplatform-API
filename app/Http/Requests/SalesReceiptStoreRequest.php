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
            
            'sales_order' => ['required', 'integer', 'exists:sales_order,id'],
            'branch_id' => ['required', 'integer','exists:branches,id'],
            'sales_invoice' => ['required', 'integer','exists:sales_invoice,id'],
            'payment_mode_id' => ['required', 'integer','exists:payment_modes,id'],
            'customer_id' => ['required', 'integer','exists:customers,id'],
            'store_id' => ['required', 'integer','exists:stores,id'],
            'sales_receipt_number' => ['string', 'unique:sales_receipt_number'],
            'total_amount' => ['required', 'string',],
            'amount_paid' => ['required', 'string'],
            'receipt_date' => ['string'],
            
        ];
    }
}
