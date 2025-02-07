<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SettleCreditStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'customer_id'    => 'required|exists:customers,id', // Ensure it exists
            'amount'            => 'nullable|numeric',         // Ensure it exists
            'narration'   => 'nullable|string', 
            'inflow_date'   => 'nullable|string',     
            'inflow_status'   => 'required|exists:inflow_status,id',                // Must be a positive integer
        ];
    }
}
