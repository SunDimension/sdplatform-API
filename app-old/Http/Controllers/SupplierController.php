<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Http\Requests\StoreSupplierRequest;
use App\Http\Requests\UpdateSupplierRequest;
use App\Http\Resources\SupplierResource;
use App\Http\Resources\SupplierCollection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    /**
     * Display a listing of suppliers.
     */
    public function index(Request $request)
{
    $query = Supplier::with('bank');

    // Filter by supplier_id (for dropdown filter)
    if ($request->filled('supplier_id')) {
        $query->where('supplier_id', $request->supplier_id);
    }

    // Search
    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function ($q) use ($search) {
            $q->where('supplier_name', 'like', "%{$search}%")
              ->orWhere('supplier_code', 'like', "%{$search}%")
              ->orWhere('contact_person', 'like', "%{$search}%")
              ->orWhere('email', 'like', "%{$search}%");
        });
    }

    // Filter by status
    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }

    // Filter by sync status
    if ($request->filled('sync_status')) {
        $query->where('sync_status', $request->sync_status);
    }

    // Sorting
    $sortBy = $request->get('sort_by', 'created_at');
    $sortOrder = $request->get('sort_order', 'desc');
    $query->orderBy($sortBy, $sortOrder);

    // Pagination
    $perPage = $request->get('per_page', 15);
    
    // Return all if per_page is very high (for dropdown)
    if ($perPage >= 1000) {
        $suppliers = $query->get();
        return SupplierResource::collection($suppliers);
    }

    // Otherwise paginate
    $suppliers = $query->paginate($perPage);

    return new SupplierCollection($suppliers);
}

    /**
     * Store a newly created supplier.
     */
    public function store(StoreSupplierRequest $request)
    {
        $supplier = Supplier::create($request->validated());

        return (new SupplierResource($supplier))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Display the specified supplier.
     */
    public function show(Supplier $supplier)
    {
        return new SupplierResource($supplier);
    }

    /**
     * Update the specified supplier.
     */
    public function update(Request $request, Supplier $supplier)
{
    $supplier->update($request->all());
    return response()->json($supplier);
}


    /**
     * Remove the specified supplier.
     */
    public function destroy(Supplier $supplier): JsonResponse
    {
        $supplier->delete();

        return response()->json([
            'message' => 'Supplier deleted successfully'
        ], 200);
    }

    /**
     * Bulk delete suppliers.
     */
    public function bulkDelete(Request $request): JsonResponse
    {
        $request->validate([
            'ids' => ['required', 'array'],
            'ids.*' => ['required', 'integer', 'exists:suppliers,supplier_id']
        ]);

        Supplier::whereIn('supplier_id', $request->ids)->delete();

        return response()->json([
            'message' => 'Suppliers deleted successfully',
            'deleted_count' => count($request->ids)
        ], 200);
    }

    /**
     * Update supplier status.
     */
    public function updateStatus(Request $request, Supplier $supplier)
    {
        $request->validate([
            'status' => ['required', 'in:active,inactive']
        ]);

        $supplier->update(['status' => $request->status]);

        return new SupplierResource($supplier);
    }
}