<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PaymentReceivedStoreRequest extends FormRequest
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
            'amount_received' => ['required', 'string'],
            'bank_charges' => ['required', 'numeric'],
            'payment_number' => ['required', 'string'],
            'deposit_bank_id' => ['required', 'integer', 'exists:deposit_banks,id'],
            'payment_mode_id' => ['required', 'integer', 'exists:payment_modes,id'],
            'invoice_number' => ['required', 'string'],
        ];
    }
}
