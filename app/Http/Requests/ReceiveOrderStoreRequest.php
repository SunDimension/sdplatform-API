<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReceiveOrderStoreRequest extends FormRequest
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
            'purchase_order_number' => ['nullable', 'string'],
            'receive_date' => ['required'],
            'store_id' => ['required', 'integer', 'exists:stores,id'],
            'branch_id' => ['required', 'integer', 'exists:branches,id'],
            'vendor_id' => ['required', 'integer'],
            'status' => ['nullable', 'string'],
            'created_by' => ['nullable'],
            'modified_by' => ['nullable'],
            'deleted_by' => ['nullable'],
            'items' => ['required'],
            'items.*.quantity' => ['required', 'numeric'],
            'items.*.unit_price' => ['required', 'numeric'],
            'items.*.product_id' => ['required', 'integer', 'exists:create_items,id'],
            'items.*.description' => ['nullable', 'string'],
            'items.*.unit_measurement' => ['nullable', 'integer'],
        ];
    }
}
