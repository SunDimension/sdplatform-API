<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SalesOrderUpdateRequest extends FormRequest
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
            'sales_order_number' => ['string', 'unique:sales_order_number'],
            'customer_id' => ['required', 'integer', 'exists:customers,id'],
            'branch_id' => ['required', 'string','exists:branches,id'],
            'store_id' => ['required', 'string','exists:stores,id'],
            'total_amount' => ['required', 'integer',],
            'credit_limit' => ['required', 'integer','exists:credit_limit,id'],
            'credit_amount' => ['string'],
            'credit_balance' => ['string'],
        ];
    }
}
