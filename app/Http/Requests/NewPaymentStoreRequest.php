<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class NewPaymentStoreRequest extends FormRequest
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
            'branch_id' => ['required', 'integer', 'exists:branches,id'],
            'warehouse_id' => ['required', 'integer', 'exists:warehouses,id'],
            'payment_amount' => ['required', 'string'],
            'payment_mode_id' => ['required', 'integer', 'exists:payment_modes,id'],
            'description' => ['required', 'string'],
        ];
    }
}
