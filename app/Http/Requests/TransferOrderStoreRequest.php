<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TransferOrderStoreRequest extends FormRequest
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
            'transfer_order_number' => ['required', 'string'],
            'transfer_date' => ['required'],
            'transfer_reason' => ['required', 'string'],
            'source_id' => ['required', 'integer', 'exists:warehouses,id'],
            'destination_id' => ['required', 'integer', 'exists:warehouses,id'],
            'image_url' => ['required', 'string'],
            'transfer_quantity' => ['required', 'string'],
            'item_id' => ['required', 'integer', 'exists:create_items,id'],
        ];
    }
}
