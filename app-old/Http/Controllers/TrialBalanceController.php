<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;

class TrialBalanceController extends Controller
{
    /**
     * Get Trial Balance using SQL View
     * RECOMMENDED APPROACH - Uses database view for optimal performance
     */
    public function index(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'as_of_date' => 'nullable|date',
            'account_type' => 'nullable|string|in:Asset,Liability,Equity,Revenue,Expense',
            'show_zero_balances' => 'nullable|boolean',
        ]);

        $asOfDate = $validated['as_of_date'] ?? null;
        $accountType = $validated['account_type'] ?? null;
        $showZeroBalances = $validated['show_zero_balances'] ?? false;

        // Base query using the view
        $query = DB::table('trial_balance_view as tb')
            ->select(
                'tb.account_id',
                'tb.account_code',
                'tb.account_name',
                'tb.account_type_id',
                'tb.account_type',
                'tb.normal_balance',
                'tb.total_debits',
                'tb.total_credits',
                'tb.balance'
            );

        // Filter by date if provided
        if ($asOfDate) {
            $query = $this->getTrialBalanceAsOfDate($asOfDate, $accountType, $showZeroBalances);
            $results = $query;
        } else {
            // Filter by account type name
            if ($accountType) {
                $query->where('tb.account_type', $accountType);
            }

            // Hide zero balances
            if (!$showZeroBalances) {
                $query->where(function ($q) {
                    $q->where('tb.total_debits', '>', 0)
                        ->orWhere('tb.total_credits', '>', 0);
                });
            }

            $results = $query->orderBy('tb.account_code')->get();
        }

        // Calculate totals
        $totalDebits = $results->sum('total_debits');
        $totalCredits = $results->sum('total_credits');
        $difference = $totalDebits - $totalCredits;

        return response()->json([
            'data' => $results,
            'summary' => [
                'total_debits' => round($totalDebits, 2),
                'total_credits' => round($totalCredits, 2),
                'difference' => round($difference, 2),
                'is_balanced' => abs($difference) < 0.01, // Allow for rounding
                'as_of_date' => $asOfDate ?? 'Current',
                'account_type_filter' => $accountType,
            ],
            'meta' => [
                'generated_at' => now()->toDateTimeString(),
                'total_accounts' => $results->count(),
            ]
        ]);
    }

    /**
     * Get Trial Balance as of a specific date
     */
    private function getTrialBalanceAsOfDate(
        string $asOfDate,
        ?string $accountType = null,
        bool $showZeroBalances = false
    ) {
        $query = DB::table('ledger_accounts as la')
            ->leftJoin('account_types as at', 'la.account_type_id', '=', 'at.id')
            ->leftJoin('ledger_postings as lp', function ($join) use ($asOfDate) {
                $join->on('la.account_id', '=', 'lp.account_id')
                    ->where('lp.posting_date', '<=', $asOfDate);
            })
            ->select(
                'la.account_id',
                'la.account_code',
                'la.account_name',
                'la.account_type_id',
                'at.account_type as account_type',
                'at.normal_balance',
                DB::raw('COALESCE(SUM(lp.debit_amount), 0) as total_debits'),
                DB::raw('COALESCE(SUM(lp.credit_amount), 0) as total_credits'),
                DB::raw('COALESCE(SUM(lp.debit_amount), 0) - COALESCE(SUM(lp.credit_amount), 0) as balance')
            )
            ->groupBy('la.account_id', 'la.account_code', 'la.account_name', 'la.account_type_id', 'at.account_type', 'at.normal_balance');

        // Filter by account type name
        if ($accountType) {
            $query->where('at.account_type', $accountType);
        }

        // Hide zero balances
        if (!$showZeroBalances) {
            $query->havingRaw('COALESCE(SUM(lp.debit_amount), 0) > 0 OR COALESCE(SUM(lp.credit_amount), 0) > 0');
        }

        return $query->orderBy('la.account_code')->get();
    }

    /**
     * Export Trial Balance to Excel/PDF
     */
    public function export(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'as_of_date' => 'nullable|date',
            'format' => 'required|in:excel,pdf,csv',
        ]);

        // Get trial balance data
        $data = $this->index($request)->getData();

        // Here you would integrate with Laravel Excel or similar
        // For now, return the data structure

        return response()->json([
            'message' => 'Export functionality - integrate with Laravel Excel',
            'format' => $validated['format'],
            'data' => $data
        ]);
    }

    /**
     * Get detailed drill-down for an account
     */
    public function accountDetail(Request $request, string $accountId): JsonResponse
    {
        $validated = $request->validate([
            'from_date' => 'nullable|date',
            'to_date' => 'nullable|date',
        ]);

        $query = DB::table('ledger_postings as lp')
            ->join('journal_lines as jl', 'lp.line_id', '=', 'jl.line_id')
            ->join('journal_entry as je', 'jl.journal_id', '=', 'je.journal_id')
            ->join('transactions as t', 'je.transaction_id', '=', 't.transaction_id')
            ->join('ledger_accounts as la', 'lp.account_id', '=', 'la.account_id')
            ->where('lp.account_id', $accountId)
            ->select(
                'lp.posting_id',
                'lp.posting_date',
                't.transaction_type',
                't.reference_id',
                'je.description',
                'lp.debit_amount',
                'lp.credit_amount',
                DB::raw('@running_balance := @running_balance + lp.debit_amount - lp.credit_amount as running_balance')
            );

        // Date filters
        if ($validated['from_date'] ?? null) {
            $query->where('lp.posting_date', '>=', $validated['from_date']);
        }

        if ($validated['to_date'] ?? null) {
            $query->where('lp.posting_date', '<=', $validated['to_date']);
        }

        // Initialize running balance variable
        DB::statement('SET @running_balance = 0');

        $transactions = $query->orderBy('lp.posting_date')
            ->orderBy('lp.created_at')
            ->get();

        // Get account info
        $account = DB::table('ledger_accounts')
            ->where('account_id', $accountId)
            ->first();

        return response()->json([
            'account' => $account,
            'transactions' => $transactions,
            'summary' => [
                'total_debits' => $transactions->sum('debit_amount'),
                'total_credits' => $transactions->sum('credit_amount'),
                'final_balance' => $transactions->last()->running_balance ?? 0,
            ]
        ]);
    }
}
