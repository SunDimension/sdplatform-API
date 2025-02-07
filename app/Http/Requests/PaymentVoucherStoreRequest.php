<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PaymentVoucherStoreRequest extends FormRequest
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
            'product_id' => ['nullable', 'integer', 'exists:products,id'],
            'expense_date' => ['required'],
            'amount' => ['nullable', 'string'],
            'description' => ['required', 'string'],
            'branch_id' => ['nullable', 'integer', 'exists:branches,id'],
            'warehouse_id' => ['required', 'integer', 'exists:warehouses,id'],
            'tax_id' => ['required', 'integer', 'exists:taxes,id'],
            'vendor_id' => ['required', 'integer', 'exists:vendors,id'],
            'payment_mode_id' => ['required', 'integer', 'exists:payment_modes,id'],
            'expense_account_id' => ['required', 'string', 'exists:accounts,id'],
            'voucher_entries.*' => ['required'],
        ];
    }
}
