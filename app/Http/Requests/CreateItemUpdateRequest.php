<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateItemUpdateRequest extends FormRequest
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
            'name' => ['required', 'string'],
            'item_category_id' => ['required', 'integer', 'exists:item_categories,id'],
            'item_type_id' => ['required', 'integer', 'exists:item_types,id'],
            'description' => ['required', 'string'],
            'batch_number' => ['required', 'string'],
            'unit_id' => ['required', 'integer', 'exists:units,id'],
            'brand_id' => ['required', 'integer', 'exists:brands,id'],
            'cost_price' => ['required', 'numeric'],
            'selling_price' => ['required', 'numeric'],
            'quantity' =>['required','string'],
            'reorder_level' => ['required', 'string'],
            'dimension_id' => ['required', 'integer', 'exists:dimensions,id'],
            'weight_id' => ['required', 'integer', 'exists:weights,id'],
            'branch_id' => ['required', 'integer', 'exists:branches,id'],
            'warehouse' => ['required'],
            'vendor_id' => ['required', 'integer', 'exists:vendors,id'],
            'image_url' => ['required', 'string'],
            'barcode' => ['string','nullable','unique:barcode'],
            'store_id' => ['required','integer', 'exist:stores,id']
        ];
    }
}
