<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SalesInvoiceStoreRequest extends FormRequest
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
            'sales_order_id' => ['required', 'integer', 'exists:sales_order,id'],
            'sales_invoice_number' => ['string', 'unique:sales_invoice_number'],
            'invoice_amount' => ['string'],
            'invoice_date' => ['date'],
        ];
    }
}
