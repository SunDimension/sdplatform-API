<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ItemPriceUpdateRequest extends FormRequest
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
            'selling_price' => ['numeric'],
            'change_date' => ['date'],
            'product_id' => ['required','integer','exists:create_items,id'],
            'store_item_id' => ['required','integer', 'exists:store_items,id'],
            'user_id' => ['required', 'integer','exists:users,id']
        ];
    }
}
