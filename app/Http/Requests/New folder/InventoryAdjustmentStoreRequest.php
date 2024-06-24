<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InventoryAdjustmentStoreRequest extends FormRequest
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
            'item_id' => ['required', 'integer', 'exists:items,id'],
            'adjustment_type_id' => ['required', 'integer', 'exists:adjustment_types,id'],
            'date' => ['required'],
            'reason_id' => ['required', 'integer', 'exists:reasons,id'],
            'branch_id' => ['required', 'integer', 'exists:branches,id'],
            'warehouse_id' => ['required', 'integer', 'exists:warehouses,id'],
            'description' => ['required', 'string'],
            'item_category_id' => ['required', 'integer', 'exists:item_categories,id'],
            'cost_price' => ['required', 'numeric'],
            'selling_price' => ['required', 'numeric'],
            'quantity' => ['required', 'string'],
        ];
    }
}
