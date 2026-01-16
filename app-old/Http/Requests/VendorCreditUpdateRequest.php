<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VendorCreditUpdateRequest extends FormRequest
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
            'vendor_id' => ['required', 'integer', 'exists:vendors,id'],
            'warehouse_id' => ['required', 'integer', 'exists:warehouses,id'],
            'credit_number' => ['required', 'string'],
            'purchase_order_number' => ['required', 'string'],
            'vendor_credit_date' => ['required', 'date'],
        ];
    }
}
