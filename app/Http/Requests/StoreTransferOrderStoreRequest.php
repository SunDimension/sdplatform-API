<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTransferOrderStoreRequest extends FormRequest
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
            'transfer_date' => ['required'],
            'source_store_id' => ['required', 'integer', 'exists:stores,id'],
            'destination_store_id' => ['required', 'integer', 'exists:stores,id'],
            'items' => ['required', 'array'],
            'items.*.product_id' => ['required', 'integer', 'exists:create_items,id'],
            'items.*.quantity' => ['required', 'integer'],
            'items.*.quantity_pieces' => ['required', 'integer'],
            'items.*.unit_price' => ['required', 'numeric'],
            'items.*.description' => ['nullable', 'string'],
        ];
    }
}
