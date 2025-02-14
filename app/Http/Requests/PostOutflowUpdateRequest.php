<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PostOutflowUpdateRequest extends FormRequest
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
            'org_bank'    => 'required|exists:banks,id', // Ensure it exists
            'beneficiary'           => 'nullable|string',       // Ensure it exists
            'amount'            => 'nullable|numeric',         // Ensure it exists
            'account_name'         => 'nullable|string',      // Ensure it exists
            'account_number'        => 'nullable|numeric',                      // Must be a valid date
            'bene_bank'      => 'required|exists:banks,id',   // Ensure it exists
            'narration'   => 'nullable|string', 
            'outflow_date'   => 'nullable|date',     
            'outflow_mode'   => 'required|exists:banks,id',                // Must be a positive integer
        ];
    }
}
