<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class JournalEntryDetailStoreRequest extends FormRequest
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
            'journal_entry_id' => ['required'],
            'journal_type_id' => ['required'],
            'amount' => ['required', 'numeric'],
            'description' => ['required', 'string'],
            'account_id' => ['required'],
            'account_no' => ['nullable', 'string'],
            'created_by' => ['nullable'],
            'modified_by' => ['nullable'],
            'deleted_by' => ['nullable'],
        ];
    }
}
