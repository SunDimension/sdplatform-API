<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PostInflowUpdateRequest extends FormRequest
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
            'bank_id'    => 'nullable', // Ensure it exists
            'customer_id' => 'required|exists:customers,id',
            'amount'     => 'nullable|numeric',         // Ensure it exists
            'narration'   => 'nullable|string', 
            'inflow_date'   => 'nullable|date',     
           
        ];
    }
}
