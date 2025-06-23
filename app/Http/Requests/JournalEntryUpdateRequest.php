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
            'payment_date' => ['required', 'date'],
            'store_id' => ['required', 'integer', 'exists:stores,id'],
            'branch_id' => ['required', 'integer', 'exists:branches,id'],
            'vendor_id' => ['required', 'integer', 'exists:vendors,id'],
            'created_by' => ['nullable'],
            'modified_by' => ['nullable'],
            'deleted_by' => ['nullable'],
        ];
    }
} 