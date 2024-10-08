<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PostInflowStoreRequest extends FormRequest
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
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
        public function rules(): array
    
    {
        return [
            'bank_id'    => 'required|exists:banks,id', // Ensure it exists
            'amount'            => 'nullable|numeric',         // Ensure it exists
            'narration'   => 'nullable|string', 
            'inflow_date'   => 'nullable|date',     
            'inflow_status'   => 'required|exists:inflow_status,id',                // Must be a positive integer
        ];
    }
}
