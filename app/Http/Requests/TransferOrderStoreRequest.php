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
            'transfer_order_number' => ['string', 'unique:transfer_order_number'], // Nullable if not provided
            'transfer_date' => ['required', 'date'], // Date format validation
            'transfer_reason' => ['required', 'integer'], // Use 'in' rule if specific values are needed
            'source_id' => ['required', 'integer', 'exists:stores,id'],
            'destination_id' => ['required', 'integer', 'exists:stores,id'],
            'image_url' => ['nullable', 'string'], // Nullable if not provided
            'transfer_quantity' => ['required', 'numeric'], // Numeric validation
            'item_id' => ['required', 'integer', 'exists:create_items,id'],
            'created_by' => ['nullable', 'integer', 'exists:users,id'], // Optional, must exist in users table
            'updated_by' => ['nullable', 'integer', 'exists:users,id'],
        ];
    }
}
