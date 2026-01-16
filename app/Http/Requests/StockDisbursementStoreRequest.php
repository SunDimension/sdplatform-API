<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StockDisbursementStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Adjust with proper policy if needed
    }

    public function rules(): array
    {
        return [
            'disbursement_date' => 'required|date',
          'disbursement_type' => 'required|string|max:50',
            'branch_id' => 'required|exists:branches,id',
            'issued_by' => 'nullable|exists:users,id',
            'approved_by' => 'nullable|exists:users,id',
            'remarks' => 'nullable|string|max:1000',

            // Items array
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:goods_recieved_items,product_id', // Changed to products table (common practice)
            'items.*.quantity_issued' => 'required|integer|min:1',
            'items.*.quantity_damaged' => 'nullable|integer|min:0', // Optional damaged quantity
            'items.*.unit_cost' => 'required|numeric|min:0',
            'items.*.batch_number' => 'nullable|string|max:100',
            'items.*.expiry_date' => 'nullable|date|after_or_equal:today',
        ];
    }

    public function messages(): array
    {
        return [
            'disbursement_date.required' => 'Disbursement date is required.',
            'disbursement_type.required' => 'Disbursement type is required.',
            'disbursement_type.in' => 'Invalid disbursement type selected.',
            'branch_id.required' => 'Please select a branch.',
            'branch_id.exists' => 'The selected branch is invalid.',

            'items.required' => 'At least one item must be added.',
            'items.min' => 'At least one item must be added.',

            'items.*.product_id.required' => 'Product is required for each item.',
            'items.*.product_id.exists' => 'Selected product does not exist.',

            'items.*.quantity_issued.required' => 'Quantity issued is required.',
            'items.*.quantity_issued.integer' => 'Quantity issued must be a whole number.',
            'items.*.quantity_issued.min' => 'Quantity issued must be at least 1.',

            'items.*.quantity_damaged.integer' => 'Damaged quantity must be a whole number.',
            'items.*.quantity_damaged.min' => 'Damaged quantity cannot be negative.',

            'items.*.unit_cost.required' => 'Unit cost is required for each item.',
            'items.*.unit_cost.numeric' => 'Unit cost must be a valid number.',
            'items.*.unit_cost.min' => 'Unit cost cannot be negative.',

            'items.*.expiry_date.date' => 'Expiry date must be a valid date.',
            'items.*.expiry_date.after_or_equal' => 'Expiry date cannot be in the past.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation()
    {
        // Optional: Trim strings, normalize dates, etc.
        if ($this->has('remarks')) {
            $this->merge([
                'remarks' => trim($this->remarks),
            ]);
        }
    }
}
