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
            'journal_entry_id' => ['required', 'integer', 'exists:journal_entries,id'],
            'journal_type_id' => ['required', 'integer', 'exists:journal_types,id'],
            'amount' => ['required', 'numeric'],
            'description' => ['required', 'string'],
            'account_id' => ['required', 'string'],
            'account_no' => ['required', 'string'],
            'created_by' => ['required', 'string'],
            'modified_by' => ['required', 'string'],
            'deleted_by' => ['required'],
        ];
    }
}
