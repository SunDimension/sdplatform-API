<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DeliveryStoreRequest extends FormRequest
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
            'customer_id' => ['required', 'integer', 'exists:customers,id'],
            'sales_order_number' => ['required', 'string'],
            'delivery_order_number' => ['required', 'string'],
            'delivery_date' => ['required'],
            'carrier_id' => ['required', 'integer', 'exists:carriers,id'],
            'notes' => ['required', 'string'],
        ];
    }
}
