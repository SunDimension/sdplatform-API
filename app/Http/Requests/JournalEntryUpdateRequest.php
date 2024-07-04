<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class JournalEntryUpdateRequest extends FormRequest
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
            'description' => ['required', 'string'],
            'payment_date' => ['required'],
            'warehouse_id' => ['required', 'integer', 'exists:warehouses,id'],
            'vendor_id' => ['required', 'string'],
            'created_by' => ['required', 'string'],
            'modified_by' => ['required', 'string'],
            'deleted_by' => ['required'],
        ];
    }
}
