<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReturnItemStoreRequest extends FormRequest
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
            
            'release_id' => ['required', 'integer', 'exists:release,id'],
            'branch_id' => ['required', 'string','exists:branches,id'],
            'store_id' => ['required', 'string','exists:stores,id'],
            'sales_receipt_id' => ['required', 'integer','exists:sales_receipts,id'],
            'return_date' => ['date'],
        ];
    }
}
