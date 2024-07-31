<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateItemStoreRequest extends FormRequest
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
            'name' => ['', 'string'],
            'item_category_id' => ['', 'integer', 'exists:item_categories,id'],
            'item_type_id' => ['', 'integer', 'exists:item_types,id'],
            'description' => ['', 'string'],
            'batch_number' => ['', 'string'],
            'unit_id' => ['', 'integer', 'exists:units,id'],
            'brand_id' => ['', 'integer', 'exists:brands,id'],
            'cost_price' => ['', 'numeric'],
            'selling_price' => ['', 'numeric'],
            'quantity' =>['','string'],
            'reorder_level' => ['', 'string'],
            'dimension_id' => ['', 'integer', 'exists:dimensions,id'],
            'weight_id' => ['', 'integer', 'exists:weights,id'],
            'branch_id' => ['', 'integer', 'exists:branches,id'],
            'warehouse' => [''],
            'vendor_id' => ['', 'integer', 'exists:vendors,id'],
            'image_url' => ['', 'string'],
            'barcode' => ['', 'string'],
            'store_id' => ['','integer', 'exist:stores,id']
        ];
    }
}
