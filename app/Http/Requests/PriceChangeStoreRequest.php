<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PriceChangeStoreRequest extends FormRequest
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
            'details' => 'required|array|min:1', // Validate as an array
            'details.*.product_id' => 'required|exists:create_items,id',
            'details.*.cost_price' => 'required|numeric|min:0',
            'details.*.new_selling_price' => 'required|numeric|min:0',
            'details.*.current_selling_price' => 'required|numeric|min:0',
            'details.*.mark_up' => 'required|numeric|min:0',
            'store_id' => ['required', 'integer'],
            'branch_id' => ['required', 'integer'],
            'change_reason_id' => ['required', 'integer'],
            'status' => ['nullable'],
            'approved_by' => ['nullable', 'string'],
            'approval_date' => ['nullable'],
            'comment' => ['nullable', 'string'],
            'created_by' => ['nullable', 'string'],
        ];
    }
}
