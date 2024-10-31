<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTransferOrderUpdateRequest extends FormRequest
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
            'order_number' => ['required', 'string'],
            'transfer_date' => ['required'],
            'source_branch_id' => ['required', 'integer', 'exists:source_branches,id'],
            'source_store_id' => ['required', 'integer', 'exists:source_stores,id'],
            'destination_branch_id' => ['required', 'integer', 'exists:destination_branches,id'],
            'destination_store_id' => ['required', 'integer', 'exists:destination_stores,id'],
            'approval_stage_id' => ['required', 'integer', 'exists:approval_stages,id'],
            'source_status' => ['required', 'string'],
            'source_date_approved' => ['nullable'],
            'destination_status' => ['required', 'string'],
            'destination_date_approved' => ['nullable'],
            'created_by' => ['nullable'],
            'modified_by' => ['nullable'],
            'deleted_by' => ['nullable'],
        ];
    }
}
