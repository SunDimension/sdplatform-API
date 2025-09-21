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
            'store_id' => ['required', 'string', 'exists:stores,id'],
            'branch_id' => ['required', 'string', 'exists:branches,id'],
            'vendor_id' => ['required', 'string'],
            'status' => ['nullable', 'string'],
            'driver_name' => ['nullable', 'string'],
            'waybill_number' => ['nullable', 'string'],
            'driver_phone' => ['nullable', 'string'],
            'truck_number' => ['nullable', 'string'],
            'created_by' => ['nullable'],
            'modified_by' => ['nullable'],
            'deleted_by' => ['nullable'],
            'items' => ['required'],
            'items.*.quantity' => ['required', 'numeric'],
            'items.*.unit_price' => ['required', 'numeric'],
            'items.*.product_id' => ['required', 'string', 'exists:create_items,id'],
            'items.*.description' => ['nullable', 'string'],
            'items.*.unit_measurement' => ['nullable', 'integer'],
        ];
    }
}
