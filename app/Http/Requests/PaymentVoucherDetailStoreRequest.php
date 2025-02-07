<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PaymentVoucherDetailStoreRequest extends FormRequest
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
            'expense_account_id' => ['required', 'string'],
            'description' => ['required', 'string'],
            'amount' => ['required', 'string'],
            'quantity' => ['required', 'string'],
            'item_id' => ['required', 'integer', 'exists:items,id'],
        ];
    }
}
