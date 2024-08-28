<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReleaseUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
         return [
            
            'store_id' => ['required', 'integer', 'exists:stores,id'],
            'branch_id' => ['required', 'integer','exists:branches,id'],
            'customer_id' => ['required', 'integer','exists:customers,id'],
            'sales_receipt_id' => ['required', 'integer','exists:sales_receipts,id'],
            'release_date' => ['date'],
        ];
    }
}
