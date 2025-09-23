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
            'name' => ['required', 'string'],
            'item_category_id' => ['required', 'integer', 'exists:item_categories,id'],

            'batch_number' => ['string', 'unique:batch_number'],

            'brand_id' => ['required', 'integer', 'exists:brands,id'],

            'selling_price' => ['nullable', 'numeric', 'min:0'],

            'vendor_id' => ['required', 'string', 'exists:vendors,id'],
            'tax_id' => ['nullable', 'integer', 'exists:taxes,id'],
            'is_tax_inclusive' => ['boolean'],

        ];
    }
}
