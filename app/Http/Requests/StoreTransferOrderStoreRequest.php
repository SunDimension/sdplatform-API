<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTransferOrderStoreRequest extends FormRequest
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
            'order_number' => [ 'string'],
            'transfer_date' => ['required'],
            'source_branch_id' => ['required', 'integer', 'exists:branches,id'],
            'source_store_id' => ['required', 'integer', 'exists:stores,id'],
            'destination_branch_id' => ['required', 'integer', 'exists:branches,id'],
            'destination_store_id' => ['required', 'integer', 'exists:stores,id'],
            // 'approval_stage_id' => ['required', 'integer', 'exists:approval_stages,id'],
            'source_status' => [ 'string'],
            // 'source_date_approved' => ['nullable'],
            'destination_status' => ['string'],
            // 'destination_date_approved' => ['nullable'],
            // 'created_by' => ['nullable'],
            // 'modified_by' => ['nullable'],
            // 'deleted_by' => ['nullable'],
            'items' => ['required', 'array'],
            'items.*.product_id' => ['required', 'integer', 'exists:create_items,id'],
            'items.*.quantity' => ['required', 'integer'],
            'items.*.quantity_pieces' => ['required', 'integer'],
            'items.*.unit_price' => ['required', 'numeric'],
            'items.*.description' => ['nullable', 'string'],
        ];
    }
}
