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
            'sales_receipt_id'    => 'required|exists:sales_receipts,id', // Ensure it exists
            'branch_id'           => 'required|exists:branches,id',       // Ensure it exists
            'store_id'            => 'required|exists:stores,id',         // Ensure it exists
            'customer_id'         => 'required|exists:customers,id',      // Ensure it exists
            'release_date'        => 'required|date',                      // Must be a valid date
            'create_item_id'      => 'required|exists:create_items,id',   // Ensure it exists
            'quantity_released'   => 'required|integer|min:1',            // Must be a positive integer
        ];
    }
}
