<?php

namespace App\Http\Controllers;

use App\Models\ProductAudit;
use App\Models\Store;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ProductAuditController extends Controller
{
    public function actionTypes()
    {
        return response()->json([
            'data' => [
                ProductAudit::ACTION_CREATED,
                ProductAudit::ACTION_UPDATED,
                ProductAudit::ACTION_DELETED,
                ProductAudit::ACTION_RECEIVED,
                ProductAudit::ACTION_SOLD,
                ProductAudit::ACTION_RETURNED,
                ProductAudit::ACTION_PRICE_CHANGE,
                ProductAudit::ACTION_STOCK_ADJUSTMENT,
                ProductAudit::ACTION_RELEASED,
                ProductAudit::ACTION_PENDING_RECEIPT,
                ProductAudit::ACTION_RECEIPT_CANCELLED,
                ProductAudit::ACTION_TRANSFER_OUT_PENDING,
                ProductAudit::ACTION_TRANSFER_IN_PENDING,
                ProductAudit::ACTION_TRANSFER_OUT,
                ProductAudit::ACTION_TRANSFER_IN,
                ProductAudit::ACTION_TRANSFER_CANCELLED,
            ]
        ]);
    }

    // public function index(Request $request)
    // {
    //     $availableActionTypes = [
    //         ProductAudit::ACTION_CREATED,
    //         ProductAudit::ACTION_UPDATED,
    //         ProductAudit::ACTION_DELETED,
    //         ProductAudit::ACTION_RECEIVED,
    //         ProductAudit::ACTION_SOLD,
    //         ProductAudit::ACTION_RETURNED,
    //         ProductAudit::ACTION_PRICE_CHANGE,
    //         ProductAudit::ACTION_STOCK_ADJUSTMENT,
    //         ProductAudit::ACTION_RELEASED,
    //         ProductAudit::ACTION_PENDING_RECEIPT,
    //         ProductAudit::ACTION_RECEIPT_CANCELLED,
    //         ProductAudit::ACTION_TRANSFER_OUT_PENDING,
    //         ProductAudit::ACTION_TRANSFER_IN_PENDING,
    //         ProductAudit::ACTION_TRANSFER_OUT,
    //         ProductAudit::ACTION_TRANSFER_IN,
    //         ProductAudit::ACTION_TRANSFER_CANCELLED,
    //     ];

    //     $validated = $request->validate([
    //         'from_date' => 'nullable|date',
    //         'to_date' => 'nullable|date|after_or_equal:from_date',
    //         'store_id' => 'nullable|string|exists:stores,id',
    //         'product_id' => 'nullable|integer|exists:create_items,id',
    //         'action_type' => 'nullable|string|in:' . implode(',', $availableActionTypes),
    //         'page' => 'nullable|integer|min:1',
    //         'per_page' => 'nullable|integer|min:1|max:100',
    //     ]);

    //     try {
    //         $fromDate = Carbon::parse($validated['from_date'])->startOfDay();
    //         $toDate = Carbon::parse($validated['to_date'])->endOfDay();

    //         $query = ProductAudit::with([
    //             'product:id,name,code',
    //             'user:id,name',
    //             'store:id,name',
    //         ])->whereBetween('product_audits.created_at', [$fromDate, $toDate]);

    //         $perPage = $validated['per_page'] ?? 10;
    //         $page = $validated['page'] ?? 1;

    //         $audits = $query->orderBy('created_at', 'desc')
    //             ->paginate($perPage, ['*'], 'page', $page);

    //         $transformedAudits = $audits->getCollection()->map(function ($audit) {
    //             return [
    //                 'id' => $audit->id,
    //                 'action_type' => $audit->action_type,
    //                 'product_id' => $audit->product_id,
    //                 'product_name' => $audit->product->name ?? 'N/A',
    //                 'product_code' => $audit->product->code ?? null,
    //                 'user_id' => $audit->user_id,
    //                 'user_name' => $audit->user->name ?? 'System',
    //                 'store_id' => $audit->store_id,
    //                 'store_name' => $audit->store->name ?? 'N/A',
    //                 'quantity_change' => (float) $audit->quantity_change,
    //                 'previous_quantity' => (float) $audit->previous_quantity,
    //                 'new_quantity' => (float) $audit->new_quantity,
    //                 'price_change' => $audit->price_change ? (float) $audit->price_change : null,
    //                 'previous_price' => $audit->previous_price ? (float) $audit->previous_price : null,
    //                 'new_price' => $audit->new_price ? (float) $audit->new_price : null,
    //                 'reference_type' => $audit->reference_type,
    //                 'reference_id' => $audit->reference_id,
    //                 'notes' => $audit->notes,
    //                 'created_at' => $audit->created_at->toDateTimeString(),
    //             ];
    //         });

    //         return response()->json([
    //             'data' => $transformedAudits,
    //             'meta' => [
    //                 'current_page' => $audits->currentPage(),
    //                 'last_page' => $audits->lastPage(),
    //                 'per_page' => $audits->perPage(),
    //                 'total' => $audits->total(),
    //                 'from' => $audits->firstItem(),
    //                 'to' => $audits->lastItem(),
    //             ]
    //         ]);
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'message' => 'An error occurred while processing the request.',
    //             'errors' => [
    //                 'from_date' => ['Invalid date format or processing error.'],
    //                 'to_date' => ['Invalid date format or processing error.'],
    //             ]
    //         ], 422);
    //     }
    // }


    public function index(Request $request)
    {
        $availableActionTypes = [
            ProductAudit::ACTION_CREATED,
            ProductAudit::ACTION_UPDATED,
            ProductAudit::ACTION_DELETED,
            ProductAudit::ACTION_RECEIVED,
            ProductAudit::ACTION_SOLD,
            ProductAudit::ACTION_RETURNED,
            ProductAudit::ACTION_PRICE_CHANGE,
            ProductAudit::ACTION_STOCK_ADJUSTMENT,
            ProductAudit::ACTION_RELEASED,
            ProductAudit::ACTION_PENDING_RECEIPT,
            ProductAudit::ACTION_RECEIPT_CANCELLED,
            ProductAudit::ACTION_TRANSFER_OUT_PENDING,
            ProductAudit::ACTION_TRANSFER_IN_PENDING,
            ProductAudit::ACTION_TRANSFER_OUT,
            ProductAudit::ACTION_TRANSFER_IN,
            ProductAudit::ACTION_TRANSFER_CANCELLED,
        ];

        // Validate request parameters
        $validated = $request->validate([
            'from_date' => 'nullable|date',
            'to_date' => 'nullable|date|after_or_equal:from_date',
            'store_id' => 'nullable|string|exists:stores,id',
            'product_id' => 'nullable|integer|exists:create_items,id',
            'action_type' => 'nullable|string|in:' . implode(',', $availableActionTypes),
            'page' => 'nullable|integer|min:1',
            'per_page' => 'nullable|integer|min:1|max:100',
        ]);

        // Build the query
        $query = ProductAudit::with([
            'product:id,name',
            'user:id,name',
            'store:id,name',
        ]);

        // Apply date filters if provided
        if (!empty($validated['from_date']) || !empty($validated['to_date'])) {
            $fromDate = isset($validated['from_date'])
                ? Carbon::parse($validated['from_date'])->startOfDay()
                : Carbon::createFromTimestamp(0); // Very old date if from_date not provided
            $toDate = isset($validated['to_date'])
                ? Carbon::parse($validated['to_date'])->endOfDay()
                : Carbon::now()->endOfDay(); // Current date if to_date not provided

            $query->whereBetween('product_audits.created_at', [$fromDate, $toDate]);
        }

        // Apply store filter if provided
        if (!empty($validated['store_id'])) {
            $query->where('store_id', $validated['store_id']);
        }

        // Apply product filter if provided
        if (!empty($validated['product_id'])) {
            $query->where('product_id', $validated['product_id']);
        }

        // Apply action type filter if provided
        if (!empty($validated['action_type'])) {
            $query->where('action_type', $validated['action_type']);
        }

        // Pagination parameters
        $perPage = $validated['per_page'] ?? 10;
        $page = $validated['page'] ?? 1;

        // Execute query with pagination
        $audits = $query->orderBy('created_at', 'desc')
            ->paginate($perPage, ['*'], 'page', $page);

        // Transform the results
        $transformedAudits = $audits->getCollection()->map(function ($audit) {
            return [
                'id' => $audit->id,
                'action_type' => $audit->action_type,
                'product_id' => $audit->product_id,
                'product_name' => $audit->product->name ?? 'N/A',
                'product_code' => $audit->product->code ?? null,
                'user_id' => $audit->user_id,
                'user_name' => $audit->user->name ?? 'System',
                'store_id' => $audit->store_id,
                // 'store_name' => $audit->store->name ?? 'N/A',
                'store_name' => $audit->store ? $audit->store->name : null,
                'quantity_change' => (float) $audit->quantity_change,
                'previous_quantity' => (float) $audit->previous_quantity,
                'new_quantity' => (float) $audit->new_quantity,
                'price_change' => $audit->price_change ? (float) $audit->price_change : null,
                'previous_price' => $audit->previous_price ? (float) $audit->previous_price : null,
                'new_price' => $audit->new_price ? (float) $audit->new_price : null,
                'reference_type' => $audit->reference_type,
                'reference_id' => $audit->reference_id,
                'notes' => $audit->notes,
                'created_at' => $audit->created_at->toDateTimeString(),
            ];
        });

        return response()->json([
            'data' => $transformedAudits,
            'meta' => [
                'current_page' => $audits->currentPage(),
                'last_page' => $audits->lastPage(),
                'per_page' => $audits->perPage(),
                'total' => $audits->total(),
                'from' => $audits->firstItem(),
                'to' => $audits->lastItem(),
                'period' => [
                    'from' => $fromDate->format('Y-m-d') ?? null,
                    'to' => $toDate->format('Y-m-d') ?? null,
                ],
            ]
        ]);
    }
    public function storesWithProducts()
    {
        $stores = Store::whereHas('productAudits')
            ->with([
                'productAudits' => function ($query) {
                    $query->select('id', 'store_id', 'product_id')
                        ->distinct('product_id')
                        ->with([
                            'product' => function ($subQuery) {
                                $subQuery->select('id', 'name');
                            }
                        ]);
                }
            ])
            ->get(['id', 'name', 'branch_id']);

        $storesWithProducts = $stores->map(function ($store) {
            $products = $store->productAudits
                ->pluck('product')
                ->filter()
                ->unique('id')
                ->map(function ($product) {
                    return [
                        'create_item_id' => $product->id,
                        'name' => $product->name,
                    ];
                })
                ->values();

            return [
                'id' => $store->id,
                'name' => $store->name,
                'branch_id' => $store->branch_id,
                'items' => $products->toArray(),
            ];
        });

        return response()->json([
            'data' => $storesWithProducts->toArray(),
        ]);
    }

    public function getStoreProducts($store_id)
    {
        $validated = validator(['store_id' => $store_id], [
            'store_id' => 'required|string|exists:stores,id',
        ])->validate();

        $products = ProductAudit::where('store_id', $validated['store_id'])
            ->select('product_id')
            ->distinct()
            ->with([
                'product' => function ($query) {
                    $query->select('id', 'name');
                }
            ])
            ->get()
            ->pluck('product')
            ->filter()
            ->unique('id')
            ->map(function ($product) {
                return [
                    'create_item_id' => $product->id,
                    'name' => $product->name,
                ];
            })
            ->values();

        return response()->json([
            'data' => $products->toArray(),
        ]);
    }
}
