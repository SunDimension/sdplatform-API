<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PurchaseItemCostStoreRequest extends FormRequest
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

            'product_id' => ['required', 'string', 'exists:create_items,id'],
            'old_cost_price' => ['required', 'numeric'],
            'new_cost_price' => ['required', 'numeric'],
        ];
    }
}
