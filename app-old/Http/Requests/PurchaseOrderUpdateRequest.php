<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PurchaseOrderUpdateRequest extends FormRequest
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
            'supplier_id' => ['required', 'integer', 'exists:suppliers,supplier_id'],
            // 'pr_id' => ['nullable', 'string', 'exists:items,id'],
            'order_date' => ['required', 'date'],
            'status' => ['required', 'string'],
            'expected_delivery_date' => ['required', 'date'],
            // 'unit_id' => ['required', 'integer', 'exists:units,id'],
        ];
    }
}
