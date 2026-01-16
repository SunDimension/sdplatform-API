<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class NewPurchaseOrderStoreRequest extends FormRequest
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
            'item_id' => ['required', 'integer', 'exists:items,id'],
            'vendor_id' => ['required', 'integer', 'exists:vendors,id'],
            'branch_id' => ['required', 'string', 'exists:branches,id'],
            'payment_mode_id' => ['required', 'integer', 'exists:payment_modes,id'],
            'purchase_order_number' => ['required', 'string'],
            'purchase_amount' => ['required', 'string'],
            'purchase_date' => ['required'],
            'expected_delivery_date' => ['required', 'date'],
            'payment_type_id' => ['required', 'integer', 'exists:payment_types,id'],
        ];
    }
}
