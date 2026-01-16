<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;

class ReceiveOrderStoreRequest extends FormRequest
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
            'purchase_order_number' => ['nullable', 'string'],
            'receive_date' => ['required'],
            'store_id' => ['required', 'string', 'exists:stores,id'],
            'branch_id' => ['required', 'string', 'exists:branches,id'],
            'vendor_id' => ['required', 'string'],
            'status' => ['nullable', 'string'],
            'driver_name' => ['nullable', 'string'],
            'waybill_number' => ['nullable', 'string'],
            'driver_phone' => ['nullable', 'string'],
            'truck_number' => ['nullable', 'string'],
            'created_by' => ['nullable'],
            'modified_by' => ['nullable'],
            'deleted_by' => ['nullable'],
            'items' => ['required'],
            'items.*.quantity' => ['required', 'numeric'],
            'items.*.unit_price' => ['required', 'numeric'],
            'items.*.product_id' => ['required', 'string', 'exists:create_items,id'],
            'items.*.description' => ['nullable', 'string'],
            'items.*.unit_measurement' => ['nullable', 'integer'],
        ];
    }


    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $branchId = $this->input('branch_id');
            $storeId = $this->input('store_id');

            // Get available quantities from disbursement
            $availableStock = DB::table('stock_disbursement_items as sdi')
                ->join('stock_disbursements as sd', 'sdi.disbursement_id', '=', 'sd.disbursement_id')
                ->join('purchase_item_costs as pci', 'sdi.product_id', '=', 'pci.id')
                ->where('sd.branch_id', '=', $branchId)
                ->select(
                    'pci.product_id as create_item_id',
                    DB::raw('SUM(sdi.quantity_issued) as total_disbursed')
                )
                ->groupBy('pci.product_id')
                ->get()
                ->keyBy('create_item_id');

            // Get already received (Pending + Approved) FOR THIS BRANCH
            $receivedQuantities = DB::table('receive_items as ri')
                ->join('receive_orders as ro', 'ri.receive_order_id', '=', 'ro.id')
                ->where('ro.branch_id', '=', $branchId)  // ← CRITICAL
                ->whereIn('ro.status', ['Pending', 'Approved'])
                ->whereNull('ro.deleted_at')
                ->select(
                    'ri.product_id as create_item_id',
                    DB::raw('SUM(ri.quantity_pieces) as total_received')
                )
                ->groupBy('ri.product_id')
                ->get()
                ->keyBy('create_item_id');

            foreach ($this->input('items', []) as $index => $item) {
                $productId = $item['product_id'];
                $requestedQty = $item['quantity'];

                // Get store item to calculate piece equivalent
                $storeItem = \App\Models\StoreItem::where('create_item_id', $productId)
                    ->where('store_id', $storeId)
                    ->first();

                if (!$storeItem) {
                    $validator->errors()->add(
                        "items.{$index}.product_id",
                        "Product not found in this store"
                    );
                    continue;
                }

                // Get measurement unit
                $measurement = \App\Models\Measurement::find($item['unit_measurement']);
                if (!$measurement) {
                    continue;
                }

                // Calculate requested quantity in PIECES
                $requestedPieces = \App\Classes\StockUtil::getPieceQuivalent(
                    $measurement->name,
                    $storeItem->quantity_in_package,
                    $requestedQty
                );

                // Get disbursed and received quantities
                $disbursed = $availableStock->get($productId);
                $received = $receivedQuantities->get($productId);

                $totalDisbursed = $disbursed ? $disbursed->total_disbursed : 0;
                $totalReceived = $received ? $received->total_received : 0;
                $availableQty = $totalDisbursed - $totalReceived;

                // Validate
                if ($totalDisbursed == 0) {
                    $productName = \App\Models\CreateItem::find($productId)->name ?? 'Unknown';
                    $validator->errors()->add(
                        "items.{$index}.quantity",
                        "{$productName}: No stock has been disbursed for this product at this branch"
                    );
                } elseif ($requestedPieces > $availableQty) {
                    $productName = \App\Models\CreateItem::find($productId)->name ?? 'Unknown';
                    $validator->errors()->add(
                        "items.{$index}.quantity",
                        "{$productName}: Requested {$requestedPieces} pieces exceeds available {$availableQty} pieces (Disbursed: {$totalDisbursed}, Already received: {$totalReceived})"
                    );
                }
            }
        });
    }
}
