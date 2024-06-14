<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class NewPurchaseReceivedUpdateRequest extends FormRequest
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
            'vendor_id' => ['required', 'integer', 'exists:vendors,id'],
            'purchase_order_number' => ['required', 'string'],
            'purchase_received_number' => ['required', 'string'],
            'received_date' => ['required', 'date'],
        ];
    }
}
