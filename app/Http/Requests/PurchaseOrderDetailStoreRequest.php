<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PurchaseOrderDetailStoreRequest extends FormRequest
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
            'purchase_order_id' => ['required', 'string'],
            'item_id' => ['required', 'integer', 'exists:items,id'],
            'unit_price' => ['required', 'string'],
            'quantity' => ['required', 'string'],
            'unit_id' => ['required', 'integer', 'exists:units,id'],
        ];
    }
}
