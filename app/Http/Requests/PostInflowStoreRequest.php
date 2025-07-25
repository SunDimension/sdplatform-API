<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PostInflowStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'bank_id'      => 'required|exists:banks,id',
            'amount'       => 'nullable|numeric',
            'narration'     => 'nullable|string',
            'inflow_date'  => 'nullable|date',
            'customer_id'   => 'nullable|exists:customers,id', // Add this line
        ];
    }
}
