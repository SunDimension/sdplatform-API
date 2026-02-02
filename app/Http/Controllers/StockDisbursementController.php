<?php

namespace App\Http\Controllers;

use App\Http\Requests\StockDisbursementStoreRequest;
use App\Http\Requests\StockDisbursementUpdateRequest;
use App\Http\Resources\StockDisbursementCollection;
use App\Http\Resources\StockDisbursementResource;
use App\Models\StockDisbursement;
use App\Models\StockDisbursementItem;
use App\Models\StockMovement;
use App\Models\GoodsRecievedItem;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class StockDisbursementController extends Controller
{
    public function index(Request $request)
    {
        $validated = $request->validate([
            'branch_id' => 'nullable|exists:branches,id',
            'from_date' => 'nullable|date',
            'to_date' => 'nullable|date',
            'product_id' => 'nullable|exists:products,id',
            'disbursement_type' => 'nullable|string',
            'disbursement_number' => 'nullable|string',
            'issued_by' => 'nullable|exists:users,id',
        ]);

        $query = StockDisbursement::with([
            'stockDisbursementItems.product',
            'branch',
            'issuedByUser',
            'approvedByUser'
        ])
            ->when($validated['branch_id'] ?? null, fn($q, $v) => $q->where('branch_id', $v))
            ->when($validated['disbursement_type'] ?? null, fn($q, $v) => $q->where('disbursement_type', $v))
            ->when($validated['issued_by'] ?? null, fn($q, $v) => $q->where('issued_by', $v))
            ->when($validated['product_id'] ?? null, function ($q, $v) {
                $q->whereHas('stockDisbursementItems', fn($qq) => $qq->where('product_id', $v));
            });

        if (!empty($validated['from_date']) && !empty($validated['to_date'])) {
            $query->whereBetween('disbursement_date', [
                Carbon::parse($validated['from_date'])->startOfDay(),
                Carbon::parse($validated['to_date'])->endOfDay(),
            ]);
        }

        return new StockDisbursementCollection(
            $query->orderBy('disbursement_date', 'desc')->get()
        );
    }

    public function searchDisbursements(Request $request)

    {
        $validated = $request->validate([
            'branch_id' => 'nullable|exists:branches,id',
            'from_date' => 'nullable|date',
            'to_date' => 'nullable|date',
            'product_id' => 'nullable|exists:products,id',
            'disbursement_type' => 'nullable|string',
            'disbursement_number' => 'nullable|string',
            'issued_by' => 'nullable|exists:users,id',
        ]);

        $query = StockDisbursement::with([
            'stockDisbursementItems.product',
            'branch',
            'issuedByUser',
            'approvedByUser'
        ])
            ->when($validated['branch_id'] ?? null, fn($q, $v) => $q->where('branch_id', $v))
            ->when($validated['disbursement_type'] ?? null, fn($q, $v) => $q->where('disbursement_type', $v))
            ->when($validated['issued_by'] ?? null, fn($q, $v) => $q->where('issued_by', $v))
            ->when($validated['product_id'] ?? null, function ($q, $v) {
                $q->whereHas('stockDisbursementItems', fn($qq) => $qq->where('product_id', $v));
            });

        if (!empty($validated['from_date']) && !empty($validated['to_date'])) {
            $query->whereBetween('disbursement_date', [
                Carbon::parse($validated['from_date'])->startOfDay(),
                Carbon::parse($validated['to_date'])->endOfDay(),
            ]);
        }

        return new StockDisbursementCollection(
            $query->orderBy('disbursement_date', 'desc')->get()
        );
    }


    public function store(StockDisbursementStoreRequest $request)
    {
        try {
            DB::beginTransaction();

            $issuedBy = $request->issued_by ?? auth()->id();

            if (!$issuedBy) {
                return response()->json([
                    'message' => 'User not authenticated',
                ], Response::HTTP_UNAUTHORIZED);
            }

            // Generate unique disbursement_number
            $today = now()->format('Ymd');
            $lastNumberToday = StockDisbursement::whereDate('created_at', now())
                ->orderBy('created_at', 'desc')
                ->value('disbursement_number');

            if ($lastNumberToday && preg_match('/(\d+)$/', $lastNumberToday, $matches)) {
                $nextNumber = intval($matches[1]) + 1;
            } else {
                $nextNumber = 1;
            }

            $disbursementNumber = 'DIST-' . $today . '-' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);

            // Create main disbursement record
            $disbursement = StockDisbursement::create([
                'disbursement_date' => $request->disbursement_date ?? now()->toDateString(),
                'disbursement_type' => $request->disbursement_type,
                'branch_id'         => $request->branch_id,
                'issued_by'         => $issuedBy,
                'approved_by'       => $request->approved_by,
                'remarks'           => $request->remarks,
                'disbursement_number' => $disbursementNumber,
            ]);

            // Process items
            foreach ($request->items as $itemData) {
                $quantityIssued = $itemData['quantity_issued'] ?? 0;
                $quantityDamaged = $itemData['quantity_damaged'] ?? 0;
                $usableQuantity = $quantityIssued - $quantityDamaged;

                if ($usableQuantity < 0) {
                    throw new \Exception("Usable quantity cannot be negative for product ID: {$itemData['product_id']}");
                }

                // **Find and update the corresponding GoodsRecievedItem**
                $goodsReceivedItem = GoodsRecievedItem::where('product_id', $itemData['product_id'])
                    ->where('batch_number', $itemData['batch_number'] ?? null)
                    ->whereRaw('(quantity_received - quantity_damaged) > 0') // Only non-depleted batches
                    ->where(function ($query) use ($quantityIssued) {
                        // Only select items that have enough quantity
                        $query->whereRaw('(quantity_received - quantity_damaged) >= ?', [$quantityIssued]);
                    })
                    ->orderBy('expiry_date', 'asc') // FIFO by expiry date
                    ->first();

                if (!$goodsReceivedItem) {
                    throw new \Exception(
                        "Insufficient stock for product ID {$itemData['product_id']}, " .
                            "Batch: " . ($itemData['batch_number'] ?? 'N/A')
                    );
                }

                // Deduct from goods received item
                $availableQuantity = $goodsReceivedItem->quantity_received - $goodsReceivedItem->quantity_damaged;

                if ($quantityIssued > $availableQuantity) {
                    throw new \Exception(
                        "Cannot disburse {$quantityIssued} units. Only {$availableQuantity} available " .
                            "for product ID {$itemData['product_id']}, Batch: " . ($itemData['batch_number'] ?? 'N/A')
                    );
                }

                // Update the goods received item by reducing quantity_received
                $goodsReceivedItem->decrement('quantity_received', $quantityIssued);

                // **Mark as depleted if quantity reaches zero**
                $remainingQty = $goodsReceivedItem->fresh()->quantity_received - $goodsReceivedItem->quantity_damaged;
                if ($remainingQty <= 0) {
                    $goodsReceivedItem->update(['is_depleted' => true]);
                }

                // Create disbursement item
                $disbursementItem = StockDisbursementItem::create([
                    'disbursement_id'   => $disbursement->disbursement_id,
                    'product_id'        => $itemData['product_id'],
                    'batch_number'      => $itemData['batch_number'] ?? null,
                    'expiry_date'       => $itemData['expiry_date'] ?? null,
                    'quantity_issued'   => $quantityIssued,
                    'quantity_damaged'  => $quantityDamaged,
                    'unit_cost'         => $itemData['unit_cost'] ?? 0,
                    'total_cost'        => ($itemData['unit_cost'] ?? 0) * $quantityIssued,
                    'gr_item_id'        => $goodsReceivedItem->gr_item_id, // Track source
                ]);

                // Create stock movement only for usable quantity
                if ($usableQuantity > 0) {
                    // Replace this section in your code:

                    // Create stock movement for the full quantity issued
                    StockMovement::create([
                        'product_id'      => $itemData['product_id'],
                        'reference_type'  => 'stock_disbursement',
                        'reference_id'    => $disbursementItem->disbursement_item_id,
                        'quantity_in'     => 0,
                        'quantity_out'    => $quantityIssued, // Use full quantity issued, not usable
                        'movement_date'   => $disbursement->disbursement_date,
                        'unit_cost'       => $itemData['unit_cost'] ?? 0,
                        'status'          => 'completed',
                        'created_by'      => $issuedBy,
                        'remarks'         => $quantityDamaged > 0
                            ? "Issued: {$quantityIssued}, Damaged: {$quantityDamaged}, Usable: {$usableQuantity}"
                            : null,
                    ]);
                }
            }

            DB::commit();

            return new StockDisbursementResource(
                $disbursement->load([
                    'stockDisbursementItems.product',
                    'branch',
                    'issuedByUser',
                    'approvedByUser'
                ])
            );
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Stock Disbursement creation failed: ' . $e->getMessage());

            return response()->json([
                'message' => 'Failed to create stock disbursement',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function update(StockDisbursementUpdateRequest $request, StockDisbursement $stockDisbursement)
    {
        try {
            DB::beginTransaction();

            // **Reverse the previous disbursement by restoring goods received quantities**
            foreach ($stockDisbursement->stockDisbursementItems as $oldItem) {
                if ($oldItem->gr_item_id) {
                    $goodsReceivedItem = GoodsRecievedItem::find($oldItem->gr_item_id);
                    if ($goodsReceivedItem) {
                        // Restore the quantity that was previously disbursed
                        $goodsReceivedItem->increment('quantity_received', $oldItem->quantity_issued);
                    }
                }
            }

            $stockDisbursement->update([
                'disbursement_date' => $request->disbursement_date ?? $stockDisbursement->disbursement_date,
                'disbursement_type' => $request->disbursement_type ?? $stockDisbursement->disbursement_type,
                'branch_id'         => $request->branch_id ?? $stockDisbursement->branch_id,
                'approved_by'       => $request->approved_by ?? $stockDisbursement->approved_by,
                'remarks'           => $request->remarks ?? $stockDisbursement->remarks,
            ]);

            // Reverse existing stock movements
            StockMovement::where('reference_type', 'stock_disbursement')
                ->whereIn('reference_id', $stockDisbursement->stockDisbursementItems->pluck('disbursement_item_id'))
                ->delete();

            // Delete old items
            $stockDisbursement->stockDisbursementItems()->delete();

            // Create new items (same logic as store)
            foreach ($request->items as $itemData) {
                $quantityIssued = $itemData['quantity_issued'] ?? 0;
                $quantityDamaged = $itemData['quantity_damaged'] ?? 0;
                $usableQuantity = $quantityIssued - $quantityDamaged;

                if ($usableQuantity < 0) {
                    throw new \Exception("Usable quantity cannot be negative");
                }

                $goodsReceivedItem = GoodsRecievedItem::where('product_id', $itemData['product_id'])
                    ->where('batch_number', $itemData['batch_number'] ?? null)
                    ->where(function ($query) use ($quantityIssued) {
                        $query->whereRaw('(quantity_received - quantity_damaged) >= ?', [$quantityIssued]);
                    })
                    ->orderBy('expiry_date', 'asc')
                    ->first();

                if (!$goodsReceivedItem) {
                    throw new \Exception("Insufficient stock for product ID {$itemData['product_id']}");
                }

                $goodsReceivedItem->decrement('quantity_received', $quantityIssued);

                $item = StockDisbursementItem::create([
                    'disbursement_id'   => $stockDisbursement->disbursement_id,
                    'product_id'        => $itemData['product_id'],
                    'batch_number'      => $itemData['batch_number'] ?? null,
                    'expiry_date'       => $itemData['expiry_date'] ?? null,
                    'quantity_issued'   => $quantityIssued,
                    'quantity_damaged'  => $quantityDamaged,
                    'unit_cost'         => $itemData['unit_cost'] ?? 0,
                    'total_cost'        => ($itemData['unit_cost'] ?? 0) * $quantityIssued,
                    'gr_item_id'        => $goodsReceivedItem->gr_item_id,
                ]);

                if ($usableQuantity > 0) {
                    StockMovement::create([
                        'product_id'      => $itemData['product_id'],
                        'reference_type'  => 'stock_disbursement',
                        'reference_id'    => $item->disbursement_item_id,
                        'quantity_in'     => 0,
                        'quantity_out'    => $usableQuantity,
                        'movement_date'   => $stockDisbursement->disbursement_date,
                        'unit_cost'       => $itemData['unit_cost'] ?? 0,
                        'status'          => 'completed',
                        'created_by'      => auth()->id(),
                    ]);
                }
            }

            DB::commit();

            return new StockDisbursementResource(
                $stockDisbursement->fresh()->load([
                    'stockDisbursementItems.product',
                    'branch',
                    'issuedByUser',
                    'approvedByUser'
                ])
            );
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Stock Disbursement update failed: ' . $e->getMessage());

            return response()->json([
                'message' => 'Failed to update stock disbursement',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $disbursement = StockDisbursement::findOrFail($id);

            // **Restore goods received quantities before deleting**
            foreach ($disbursement->stockDisbursementItems as $item) {
                if ($item->gr_item_id) {
                    $goodsReceivedItem = GoodsRecievedItem::find($item->gr_item_id);
                    if ($goodsReceivedItem) {
                        $goodsReceivedItem->increment('quantity_received', $item->quantity_issued);
                    }
                }
            }

            // Delete related stock movements
            StockMovement::where('reference_type', 'stock_disbursement')
                ->whereIn('reference_id', $disbursement->stockDisbursementItems->pluck('disbursement_item_id'))
                ->delete();

            // Delete items
            $disbursement->stockDisbursementItems()->delete();

            // Delete header
            $disbursement->delete();

            DB::commit();

            return response(null, Response::HTTP_NO_CONTENT);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Stock Disbursement deletion failed: ' . $e->getMessage());

            return response()->json([
                'message' => 'Failed to delete stock disbursement',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function getAvailableQuantities(Request $request)
    {
        $branchId = (string) ($request->input('branch_id') ?? auth()->user()->branch_id);

        if (!$branchId) {
            return response()->json(['data' => []]);
        }

        $availableStock = DB::table('stock_disbursement_items as sdi')
            ->join('stock_disbursements as sd', 'sdi.disbursement_id', '=', 'sd.disbursement_id')
            ->join('purchase_item_costs as pci', 'sdi.product_id', '=', 'pci.id')
            ->where('sd.branch_id', '=', $branchId)
            ->select(
                'pci.product_id as create_item_id',
                DB::raw('SUM(sdi.quantity_issued) as total_disbursed')
            )
            ->groupBy('pci.product_id')
            ->get();

        $receivedQuantities = DB::table('receive_items as ri')
            ->join('receive_orders as ro', 'ri.receive_order_id', '=', 'ro.id')
            ->where('ro.branch_id', '=', $branchId)
            ->whereIn('ro.status', ['Pending', 'Approved'])
            ->whereNull('ro.deleted_at')
            ->select(
                'ri.product_id as create_item_id',
                DB::raw('SUM(ri.quantity_pieces) as total_received')
            )
            ->groupBy('ri.product_id')
            ->get()
            ->keyBy('create_item_id');

        $result = $availableStock->map(function ($item) use ($receivedQuantities) {
            $received = $receivedQuantities->get($item->create_item_id);
            $totalReceived = $received ? $received->total_received : 0;

            return [
                'product_id' => $item->create_item_id,
                'total_disbursed' => (int) $item->total_disbursed,
                'total_received' => (int) $totalReceived,
                'available_quantity' => max(0, $item->total_disbursed - $totalReceived)
            ];
        });

        return response()->json([
            'data' => $result,
            'debug' => [
                'branch_id' => $branchId,
                'disbursed_products' => $availableStock->pluck('create_item_id'),
                'received_products' => $receivedQuantities->keys()
            ]
        ]);
    }
}
