<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;

class ProfitLossController extends Controller
{
    /**
     * Get Profit & Loss Statement
     */
    public function searchProfitLoss(Request $request): JsonResponse
    {
        // Validate request parameters
        $validated = $request->validate([
            'from_date' => 'nullable|date',
            'to_date' => 'nullable|date',
            'category' => 'nullable|string|in:Revenue,Expense',
            'show_zero_amounts' => 'nullable|boolean',
            'group_by_type' => 'nullable|boolean',
        ]);

        // Extract validated parameters
        $fromDate = $validated['from_date'] ?? null;
        $toDate = $validated['to_date'] ?? null;
        $category = $validated['category'] ?? null;
        $showZeroAmounts = $validated['show_zero_amounts'] ?? false;
        $groupByType = $validated['group_by_type'] ?? true;

        // Handle date parsing
        $fromDate = $fromDate ? Carbon::parse($fromDate)->startOfDay() : null;
        $toDate = $toDate ? Carbon::parse($toDate)->endOfDay() : Carbon::now()->endOfDay();

        // Get P&L data using main query
        $results = $this->getProfitLossQuery($fromDate, $toDate, $category, $showZeroAmounts);

        // Calculate totals
        $totalRevenue = $results->where('category', 'Revenue')->sum('amount');
        $totalExpenses = $results->where('category', 'Expense')->sum('amount');
        $netIncome = $totalRevenue - $totalExpenses;

        // Calculate profit margin (avoid division by zero)
        $profitMargin = $totalRevenue > 0
            ? round(($netIncome / $totalRevenue) * 100, 2)
            : 0;

        // Return response
        return response()->json([
            'data' => $this->structureProfitLoss($results, $groupByType),
            'summary' => [
                'total_revenue' => round($totalRevenue, 2),
                'total_expenses' => round($totalExpenses, 2),
                'net_income' => round($netIncome, 2),
                'profit_margin' => $profitMargin,
            ],
            'meta' => [
                'period' => [
                    'from_date' => $fromDate ? $fromDate->format('Y-m-d') : 'Inception',
                    'to_date' => $toDate->format('Y-m-d'),
                ],
                'generated_at' => now()->toDateTimeString(),
                'total_line_items' => $results->count(),
            ]
        ]);
    }

    /**
     * Get Profit & Loss data
     */
    private function getProfitLossQuery($fromDate = null, $toDate = null, $category = null, $showZeroAmounts = false)
    {
        // Check if using view or direct query
        if (DB::getSchemaBuilder()->hasView('pnl_auto_view')) {
            return $this->getFromView($fromDate, $toDate, $category, $showZeroAmounts);
        }

        return $this->getFromDirectQuery($fromDate, $toDate, $category, $showZeroAmounts);
    }

    /**
     * Get data from view
     */
    private function getFromView($fromDate = null, $toDate = null, $category = null, $showZeroAmounts = false)
    {
        $query = DB::table('pnl_auto_view')
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
    private function getFromDirectQuery($fromDate = null, $toDate = null, $category = null, $showZeroAmounts = false)
    {
        $query = DB::table('accounts as a')
            ->join('journal_entries as je', 'je.account_id', '=', 'a.id')
            ->join('account_types as at', 'a.account_type_id', '=', 'at.id')
            ->select(
                'a.id as account_id',
                'a.code as account_code',
                'a.name as account_name',
                'at.name as account_type',
                DB::raw("
                    CASE 
                        WHEN at.name LIKE '%Revenue%' OR at.name LIKE '%Income%'
                            THEN SUM(je.credit_amount - je.debit_amount)
                        WHEN at.name LIKE '%Expense%' OR at.name LIKE '%Cost%'
                            THEN SUM(je.debit_amount - je.credit_amount)
                        ELSE 0
                    END AS amount
                "),
                DB::raw("
                    CASE
                        WHEN at.name LIKE '%Revenue%' OR at.name LIKE '%Income%'
                            THEN 'Revenue'
                        ELSE 'Expense'
                    END AS category
                ")
            )
            ->where(function ($q) {
                $q->where('at.name', 'LIKE', '%Revenue%')
                    ->orWhere('at.name', 'LIKE', '%Income%')
                    ->orWhere('at.name', 'LIKE', '%Expense%')
                    ->orWhere('at.name', 'LIKE', '%Cost%');
            });

        // Apply date filters
        if ($fromDate && $toDate) {
            $query->whereBetween('je.entry_date', [$fromDate, $toDate]);
        }

        // Category filter
        if ($category) {
            if ($category === 'Revenue') {
                $query->where(function ($q) {
                    $q->where('at.name', 'LIKE', '%Revenue%')
                        ->orWhere('at.name', 'LIKE', '%Income%');
                });
            } else {
                $query->where(function ($q) {
                    $q->where('at.name', 'LIKE', '%Expense%')
                        ->orWhere('at.name', 'LIKE', '%Cost%');
                });
            }
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
     * Structure Profit & Loss data
     */
    private function structureProfitLoss($results, $groupByType = true)
    {
        if (!$groupByType) {
            return $results->values();
        }

        $revenue = $results->where('category', 'Revenue');
        $expenses = $results->where('category', 'Expense');

        // Group revenue by account type
        $revenueByType = $revenue->groupBy('account_type')->map(function ($items, $type) {
            return [
                'type' => $type,
                'accounts' => $items->values(),
                'subtotal' => round($items->sum('amount'), 2),
            ];
        })->values();

        // Group expenses by account type
        $expensesByType = $expenses->groupBy('account_type')->map(function ($items, $type) {
            return [
                'type' => $type,
                'accounts' => $items->values(),
                'subtotal' => round($items->sum('amount'), 2),
            ];
        })->values();

        return [
            'revenue' => [
                'categories' => $revenueByType,
                'total' => round($revenue->sum('amount'), 2),
            ],
            'expenses' => [
                'categories' => $expensesByType,
                'total' => round($expenses->sum('amount'), 2),
            ],
        ];
    }

    /**
     * Get Comparative P&L
     */
    public function comparative(Request $request): JsonResponse
    {
        // Validate required parameters
        $validated = $request->validate([
            'current_from' => 'required|date',
            'current_to' => 'required|date|after_or_equal:current_from',
            'previous_from' => 'nullable|date',
            'previous_to' => 'nullable|date',
        ]);

        // Parse dates
        $currentFrom = Carbon::parse($validated['current_from'])->startOfDay();
        $currentTo = Carbon::parse($validated['current_to'])->endOfDay();

        // Calculate previous period
        $daysDiff = $currentFrom->diffInDays($currentTo) + 1;
        $previousFrom = isset($validated['previous_from'])
            ? Carbon::parse($validated['previous_from'])->startOfDay()
            : $currentFrom->copy()->subDays($daysDiff);
        $previousTo = isset($validated['previous_to'])
            ? Carbon::parse($validated['previous_to'])->endOfDay()
            : $currentFrom->copy()->subDay()->endOfDay();

        // Get data for both periods
        $currentPeriod = $this->getProfitLossQuery($currentFrom, $currentTo);
        $previousPeriod = $this->getProfitLossQuery($previousFrom, $previousTo);

        // Calculate summary
        $currentRevenue = $currentPeriod->where('category', 'Revenue')->sum('amount');
        $currentExpenses = $currentPeriod->where('category', 'Expense')->sum('amount');
        $currentNetIncome = $currentRevenue - $currentExpenses;

        $previousRevenue = $previousPeriod->where('category', 'Revenue')->sum('amount');
        $previousExpenses = $previousPeriod->where('category', 'Expense')->sum('amount');
        $previousNetIncome = $previousRevenue - $previousExpenses;

        // Calculate variance
        $revenueVariance = $currentRevenue - $previousRevenue;
        $expensesVariance = $currentExpenses - $previousExpenses;
        $netIncomeVariance = $currentNetIncome - $previousNetIncome;

        // Calculate percentage changes
        $revenueChange = $previousRevenue > 0
            ? round(($revenueVariance / $previousRevenue) * 100, 2)
            : 0;
        $expensesChange = $previousExpenses > 0
            ? round(($expensesVariance / $previousExpenses) * 100, 2)
            : 0;
        $netIncomeChange = abs($previousNetIncome) > 0
            ? round(($netIncomeVariance / abs($previousNetIncome)) * 100, 2)
            : 0;

        return response()->json([
            'current_period' => [
                'data' => $this->structureProfitLoss($currentPeriod, true),
                'summary' => [
                    'revenue' => round($currentRevenue, 2),
                    'expenses' => round($currentExpenses, 2),
                    'net_income' => round($currentNetIncome, 2),
                ],
                'period' => [
                    'from' => $currentFrom->format('Y-m-d'),
                    'to' => $currentTo->format('Y-m-d'),
                    'days' => $daysDiff,
                ],
            ],
            'previous_period' => [
                'data' => $this->structureProfitLoss($previousPeriod, true),
                'summary' => [
                    'revenue' => round($previousRevenue, 2),
                    'expenses' => round($previousExpenses, 2),
                    'net_income' => round($previousNetIncome, 2),
                ],
                'period' => [
                    'from' => $previousFrom->format('Y-m-d'),
                    'to' => $previousTo->format('Y-m-d'),
                    'days' => $previousFrom->diffInDays($previousTo) + 1,
                ],
            ],
            'variance' => [
                'revenue' => [
                    'amount' => round($revenueVariance, 2),
                    'percentage' => $revenueChange,
                ],
                'expenses' => [
                    'amount' => round($expensesVariance, 2),
                    'percentage' => $expensesChange,
                ],
                'net_income' => [
                    'amount' => round($netIncomeVariance, 2),
                    'percentage' => $netIncomeChange,
                ],
            ],
            'meta' => [
                'generated_at' => now()->toDateTimeString(),
            ]
        ]);
    }
}
