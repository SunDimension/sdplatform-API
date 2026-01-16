<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreItemUpdateRequest extends FormRequest
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
            'item_category_id' => ['sometimes', 'integer', 'exists:item_categories,id'],
            'quantity' => ['sometimes', 'numeric'],
            'cost_price' => ['sometimes', 'numeric'],
            'selling_price' => ['sometimes', 'numeric'],
            'reorder_level' => ['sometimes', 'string'],
            'discount' => ['sometimes', 'numeric'],
            'set_limit' => ['sometimes', 'numeric'],
            'create_item_id' => ['sometimes', 'integer', 'exists:create_items,id'],
            'store_id' => ['sometimes', 'integer', 'exists:stores,id'],
            'branch_id' => ['sometimes', 'integer', 'exists:branches,id'],
            'quantity_in_package' => ['sometimes', 'numeric', 'min:1'], // Add this line
            'selling_price_per_unit' => ['sometimes', 'numeric', 'min:0'], // Add this line
        ];
    }
}