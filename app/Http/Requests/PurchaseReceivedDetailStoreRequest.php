<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PurchaseReceivedDetailStoreRequest extends FormRequest
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
            'new_purchased_received_id' => ['required', 'integer', 'exists:new_purchased_receiveds,id'],
            'item_category_id' => ['required', 'integer', 'exists:item_categories,id'],
            'item_id' => ['required', 'integer', 'exists:items,id'],
            'unit_price' => ['required', 'string'],
            'quantity' => ['required', 'string'],
            'unit_id' => ['required', 'integer', 'exists:units,id'],
        ];
    }
}
