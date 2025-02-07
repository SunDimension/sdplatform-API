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
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
     public function rules(): array
    {
        return [
            'item_category_id' => ['required', 'integer', 'exists:item_categories,id'],
            'quantity'=>['numeric'],
            'cost_price' => ['numeric'],
            'selling_price' => ['numeric'],
            'reorder_level' => ['string'],
            'discount' => ['nullable', 'numeric'],
            'create_item_id' => ['required','integer','exists:create_items,id'],
            'store_id' => ['required','integer', 'exists:stores,id'],
            'branch_id' => ['required', 'integer','exists:branches,id']
        ];
    }
}
