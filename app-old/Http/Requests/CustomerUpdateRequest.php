<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CustomerUpdateRequest extends FormRequest
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
            'customer_type_id' => ['required', 'integer', 'exists:customer_types,id'],
            'title_id' => ['required', 'integer','exists:titles,id'],
            'surname' => ['required', 'string',],
            'middlename' => ['nullable', 'string'],  // Changed to nullable
            'firstname' => ['nullable', 'string'],
            'email' => ['string','email'],
            'address' => ['string'],
            'phone_number' => ['string'],
            'credit_limit' => ['nullable', 'integer'],
            'credit_amount' => ['nullable', 'numeric'],
            'credit_balance' => ['nullable', 'numeric'],
        ];
    }
}
