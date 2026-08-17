<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PaymentStoreRequest extends FormRequest
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
            'user_id' => ['required'],
            'subscription_id' => ['nullable'],
            'property_id' => ['nullable'],
            'currency' => ['required', 'string'],
            'gateway' => ['required', 'string'],
            'transaction_reference' => ['required', 'string'],
            'paid_at' => ['nullable'],
            'amount' => ['required', 'numeric'],
            'payment_status' => ['required', 'string'],
        ];
    }
}
