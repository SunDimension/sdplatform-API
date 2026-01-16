<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StockDisbursementUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $disbursementId = $this->route('stockDisbursement')->disbursement_id ?? $this->route('stock_disbursement');

        return [
            'disbursement_date' => 'sometimes|required|date',
            'disbursement_type' => 'sometimes|required|string',
            'branch_id' => 'sometimes|required|exists:branches,id',
            'issued_by' => 'nullable|exists:users,id',
            'approved_by' => 'nullable|exists:users,id',
            'remarks' => 'nullable|string',
            'items' => 'sometimes|array|min:1',
            'items.*.product_id' => 'required|exists:purchase_item_costs,id',
            'items.*.batch_number' => 'nullable|string',
            'items.*.expiry_date' => 'nullable|date',
            'items.*.quantity_issued' => 'required|integer|min:1',
            // 'items.*.unit_cost' => 'required|numeric|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'items.min' => 'At least one item is required',
        ];
    }
}