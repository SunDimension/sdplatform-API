<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;

class BalanceSheetController extends Controller
{
    /**
     * Get Balance Sheet
     */
    public function index(Request $request): JsonResponse
    {
        // Validate request
        $validated = $request->validate([
            'as_of_date' => 'nullable|date',
            'category' => 'nullable|string|in:Asset,Liability,Equity',
            'show_zero_amounts' => 'nullable|boolean',
            'group_by_type' => 'nullable|boolean',
        ]);

        // Extract parameters
        $asOfDate = $validated['as_of_date'] ?? null;
        $category = $validated['category'] ?? null;
        $showZeroAmounts = $validated['show_zero_amounts'] ?? false;
        $groupByType = $validated['group_by_type'] ?? true;

        // Handle date
        $asOfDate = $asOfDate
            ? Carbon::parse($asOfDate)->endOfDay()
            : Carbon::now()->endOfDay();

        // Get balance sheet data
        $results = $this->getBalanceSheetData($asOfDate, $category, $showZeroAmounts);

        // Structure data
        $structured = $this->structureBalanceSheet($results, $groupByType);

        // Calculate totals
        $totalAssets = $results->where('category', 'Asset')->sum('amount');
        $totalLiabilities = $results->where('category', 'Liability')->sum('amount');
        $totalEquity = $results->where('category', 'Equity')->sum('amount');
        $totalLiabilitiesAndEquity = $totalLiabilities + $totalEquity;

        return response()->json([
            'data' => $structured,
            'summary' => [
                'total_assets' => round($totalAssets, 2),
                'total_liabilities' => round($totalLiabilities, 2),
                'total_equity' => round($totalEquity, 2),
                'total_liabilities_and_equity' => round($totalLiabilitiesAndEquity, 2),
                'is_balanced' => abs($totalAssets - $totalLiabilitiesAndEquity) < 0.01,
                'as_of_date' => $asOfDate->format('Y-m-d'),
            ],
            'meta' => [
                'generated_at' => now()->toDateTimeString(),
                'total_line_items' => $results->count(),
            ]
        ]);
    }

    /**
     * Get Balance Sheet data
     */
    private function getBalanceSheetData($asOfDate, $category = null, $showZeroAmounts = false)
    {
        // Check if view exists
        if (DB::getSchemaBuilder()->hasView('balance_sheet_auto_view')) {
            return $this->getFromView($asOfDate, $category, $showZeroAmounts);
        }

        return $this->getFromQuery($asOfDate, $category, $showZeroAmounts);
    }

    /**
     * Get data from view
     */
    private function getFromView($asOfDate, $category = null, $showZeroAmounts = false)
    {
        $query = DB::table('balance_sheet_auto_view')
            ->select(
                'account_id',
                'account_code',
                'account_name',
                'account_type',
                'amount',
                'category'
            );

        // Filter by category
        if ($category) {
            $query->where('category', $category);
        }

        // Hide zero amounts
        if (!$showZeroAmounts) {
            $query->where('amount', '!=', 0);
        }

        return $query->orderBy('category')
            ->orderBy('account_code')
            ->get();
    }

    /**
     * Get data from direct query
     */
    private function getFromQuery($asOfDate, $category = null, $showZeroAmounts = false)
    {
        $query = DB::table('accounts as a')
            ->join('account_types as at', 'a.account_type_id', '=', 'at.id')
            ->leftJoin('journal_entries as je', function ($join) use ($asOfDate) {
                $join->on('je.account_id', '=', 'a.id')
                    ->where('je.entry_date', '<=', $asOfDate);
            })
            ->select(
                'a.id as account_id',
                'a.code as account_code',
                'a.name as account_name',
                'at.name as account_type',
                DB::raw("
                    CASE 
                        WHEN at.name LIKE '%Asset%' 
                            THEN COALESCE(SUM(je.debit_amount), 0) - COALESCE(SUM(je.credit_amount), 0)
                        ELSE COALESCE(SUM(je.credit_amount), 0) - COALESCE(SUM(je.debit_amount), 0)
                    END AS amount
                "),
                DB::raw("
                    CASE
                        WHEN at.name LIKE '%Asset%' THEN 'Asset'
                        WHEN at.name LIKE '%Liability%' THEN 'Liability'
                        WHEN at.name LIKE '%Equity%' THEN 'Equity'
                        ELSE 'Other'
                    END AS category
                ")
            )
            ->where(function ($q) {
                $q->where('at.name', 'LIKE', '%Asset%')
                    ->orWhere('at.name', 'LIKE', '%Liability%')
                    ->orWhere('at.name', 'LIKE', '%Equity%');
            });

        // Category filter
        if ($category) {
            $query->where('at.name', 'LIKE', "%{$category}%");
        }

        $query->groupBy('a.id', 'a.code', 'a.name', 'at.name');

        $results = $query->get();

        // Hide zero amounts
        if (!$showZeroAmounts) {
            $results = $results->filter(function ($item) {
                return abs($item->amount) > 0.01;
            })->values();
        }

        return $results->sortBy('account_code')->values();
    }

    /**
     * Structure Balance Sheet data
     */
    private function structureBalanceSheet($results, $groupByType = true)
    {
        if (!$groupByType) {
            return [
                'assets' => $results->where('category', 'Asset')->values(),
                'liabilities' => $results->where('category', 'Liability')->values(),
                'equity' => $results->where('category', 'Equity')->values(),
            ];
        }

        $assets = $results->where('category', 'Asset');
        $liabilities = $results->where('category', 'Liability');
        $equity = $results->where('category', 'Equity');

        // Group assets by type
        $assetsByType = $assets->groupBy('account_type')->map(function ($items, $type) {
            return [
                'type' => $type,
                'accounts' => $items->values(),
                'subtotal' => round($items->sum('amount'), 2),
            ];
        })->values();

        // Group liabilities by type
        $liabilitiesByType = $liabilities->groupBy('account_type')->map(function ($items, $type) {
            return [
                'type' => $type,
                'accounts' => $items->values(),
                'subtotal' => round($items->sum('amount'), 2),
            ];
        })->values();

        // Group equity by type
        $equityByType = $equity->groupBy('account_type')->map(function ($items, $type) {
            return [
                'type' => $type,
                'accounts' => $items->values(),
                'subtotal' => round($items->sum('amount'), 2),
            ];
        })->values();

        return [
            'assets' => [
                'categories' => $assetsByType,
                'total' => round($assets->sum('amount'), 2),
            ],
            'liabilities' => [
                'categories' => $liabilitiesByType,
                'total' => round($liabilities->sum('amount'), 2),
            ],
            'equity' => [
                'categories' => $equityByType,
                'total' => round($equity->sum('amount'), 2),
            ],
        ];
    }
}
