<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PriceChangeUpdateRequest extends FormRequest
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
            'details' => ['required', 'json'],
            'store_id' => ['required', 'string'],
            'branch_id' => ['required', 'string'],
            'change_reason_id' => ['required', 'string'],
            'status' => ['required', 'in:pending,approved,declined'],
            'approved_by' => ['nullable', 'string'],
            'approval_date' => ['nullable'],
            'comment' => ['required', 'string', 'max:1000'],
            'created_by' => ['required', 'string'],
            'created_at' => ['required'],
            'updated_at' => ['required'],
        ];
    }
}
