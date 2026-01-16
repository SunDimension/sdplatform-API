<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreItemStoreRequest extends FormRequest
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
            'item_category_id' => ['required', 'integer', 'exists:item_categories,id'],
            'quantity' => ['numeric'],
            'cost_price' => ['numeric'],
            'selling_price' => ['numeric'],
            'reorder_level' => ['string'],
            'discount' => ['numeric'],
            'set_limit' => ['numeric'],
            'create_item_id' => ['required', 'string', 'exists:create_items,id'],
            'store_id' => ['required', 'string', 'exists:stores,id'],
            'branch_id' => ['required', 'string', 'exists:branches,id'],
            'quantity_in_package' => ['required', 'numeric', 'min:1'], // Add this line
            'selling_price_per_unit' => ['required', 'numeric', 'min:0'], // Add this line
        ];
    }
}
