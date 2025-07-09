<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProductAuditCollection;
use App\Models\ProductAudit;
use App\Models\CreateItem;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ProductAuditController extends Controller
{
    public function index(Request $request)
    {
        $validated = $request->validate([
            'date_from' => 'required|date',
            'date_to' => 'required|date|after_or_equal:date_from',
            'product_id' => 'required|exists:create_items,id',
            'action_type' => 'nullable|in:all,move,replenished,deleted,price_adjustment,sold,returned'
        ]);
        
        $query = ProductAudit::with(['user', 'product'])
            ->where('product_id', $validated['product_id'])
            ->whereBetween('created_at', [
                Carbon::parse($validated['date_from'])->startOfDay(),
                Carbon::parse($validated['date_to'])->endOfDay()
            ]);
            
        if ($validated['action_type'] && $validated['action_type'] !== 'all') {
            $query->where('action_type', $validated['action_type']);
        }
        
        $audits = $query->orderBy('created_at', 'desc')->get();
        
        $summary = [
            'total_received' => $audits->where('action_type', 'replenished')->sum('quantity_change'),
            'total_sold' => abs($audits->where('action_type', 'sold')->sum('quantity_change')),
            'total_returned' => $audits->where('action_type', 'returned')->sum('quantity_change'),
            'total_deleted' => abs($audits->where('action_type', 'deleted')->sum('quantity_change')),
            'price_adjustments' => $audits->where('action_type', 'price_adjustment')->count()
        ];
        
        return response()->json([
            'data' => new ProductAuditCollection($audits),
            'summary' => $summary
        ]);
    }
}
