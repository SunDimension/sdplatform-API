<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SupplierPaymentStoreRequest extends FormRequest
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
            'supplier_id' => ['required', 'string', 'exists:suppliers,supplier_id'],
            'invoice_id' => ['required', 'string', 'exists:supplier_invoices,invoice_id'],
            'payment_date' => ['required', 'date'],

            'payment_method' => ['required', 'string', 'in:Cash,Bank,Mobile Money'],
            'amount_paid' => ['required', 'numeric', 'min:0'],
            'reference_no' => ['nullable', 'string'],

        ];
    }
}
