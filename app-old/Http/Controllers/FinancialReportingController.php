<?php

namespace App\Http\Controllers;

use App\Services\FinancialReportingService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class FinancialReportingController extends Controller
{
    protected $financialReportingService;

    public function __construct(FinancialReportingService $financialReportingService)
    {
        $this->financialReportingService = $financialReportingService;
    }

    /**
     * Generate Profit and Loss Statement
     */
    public function generateProfitAndLoss(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'financial_period_id' => 'nullable|uuid|exists:financial_periods,id',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
            'format' => 'nullable|in:json,csv,pdf',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $statement = $this->financialReportingService->generateProfitAndLoss(
                $request->financial_period_id,
                $request->date_from,
                $request->date_to
            );

            $format = $request->format ?? 'json';
            $exportedStatement = $this->financialReportingService->exportStatement($statement, $format);

            return response()->json([
                'success' => true,
                'message' => 'Profit and Loss Statement generated successfully',
                'data' => $exportedStatement,
                'format' => $format,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate Profit and Loss Statement',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Generate Balance Sheet
     */
    public function generateBalanceSheet(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'financial_period_id' => 'nullable|uuid|exists:financial_periods,id',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
            'format' => 'nullable|in:json,csv,pdf',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $statement = $this->financialReportingService->generateBalanceSheet(
                $request->financial_period_id,
                $request->date_from,
                $request->date_to
            );

            $format = $request->format ?? 'json';
            $exportedStatement = $this->financialReportingService->exportStatement($statement, $format);

            return response()->json([
                'success' => true,
                'message' => 'Balance Sheet generated successfully',
                'data' => $exportedStatement,
                'format' => $format,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate Balance Sheet',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Generate Comparative Financial Statements
     */
    public function generateComparativeStatements(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'current_period_id' => 'required|uuid|exists:financial_periods,id',
            'previous_period_id' => 'required|uuid|exists:financial_periods,id',
            'format' => 'nullable|in:json,csv,pdf',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $statements = $this->financialReportingService->generateComparativeStatements(
                $request->current_period_id,
                $request->previous_period_id
            );

            $format = $request->format ?? 'json';
            $exportedStatements = $this->financialReportingService->exportStatement($statements, $format);

            return response()->json([
                'success' => true,
                'message' => 'Comparative Financial Statements generated successfully',
                'data' => $exportedStatements,
                'format' => $format,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate Comparative Financial Statements',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Generate Trial Balance
     */
    public function generateTrialBalance(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'financial_period_id' => 'nullable|uuid|exists:financial_periods,id',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
            'format' => 'nullable|in:json,csv,pdf',
            'detailed' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $detailed = $request->boolean('detailed', false);
            
            if ($detailed) {
                $statement = $this->financialReportingService->generateDetailedTrialBalance(
                    $request->financial_period_id,
                    $request->date_from,
                    $request->date_to
                );
            } else {
                $statement = $this->financialReportingService->generateTrialBalance(
                    $request->financial_period_id,
                    $request->date_from,
                    $request->date_to
                );
            }

            $format = $request->format ?? 'json';
            $exportedStatement = $this->financialReportingService->exportStatement($statement, $format);

            return response()->json([
                'success' => true,
                'message' => 'Trial Balance generated successfully',
                'data' => $exportedStatement,
                'format' => $format,
                'is_balanced' => $statement['is_balanced'],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate Trial Balance',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get trial balance summary
     */
    public function getTrialBalanceSummary(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'financial_period_id' => 'nullable|uuid|exists:financial_periods,id',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $trialBalance = $this->financialReportingService->generateTrialBalance(
                $request->financial_period_id,
                $request->date_from,
                $request->date_to
            );

            $summary = [
                'total_accounts' => $trialBalance['trial_balance']->count(),
                'total_debits' => $trialBalance['total_debits'],
                'total_credits' => $trialBalance['total_credits'],
                'difference' => $trialBalance['difference'],
                'is_balanced' => $trialBalance['is_balanced'],
                'period' => $trialBalance['period'],
                'account_types_breakdown' => $trialBalance['trial_balance']->groupBy('account.account_type')->map(function ($accounts, $type) {
                    return [
                        'count' => $accounts->count(),
                        'total_debits' => $accounts->sum('debit_balance'),
                        'total_credits' => $accounts->sum('credit_balance'),
                    ];
                }),
            ];

            return response()->json([
                'success' => true,
                'message' => 'Trial Balance summary retrieved successfully',
                'data' => $summary,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve Trial Balance summary',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get available financial periods
     */
    public function getFinancialPeriods(): JsonResponse
    {
        try {
            $periods = \App\Models\FinancialPeriod::with('financialYear')
                ->orderBy('date_from', 'desc')
                ->get()
                ->map(function ($period) {
                    return [
                        'id' => $period->id,
                        'name' => $period->name,
                        'date_from' => $period->date_from,
                        'date_to' => $period->date_to,
                        'financial_year' => $period->financialYear->name ?? null,
                        'is_active' => $period->is_active,
                    ];
                });

            return response()->json([
                'success' => true,
                'message' => 'Financial periods retrieved successfully',
                'data' => $periods,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve financial periods',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get account summary for financial reporting
     */
    public function getAccountSummary(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'account_type' => 'nullable|string|in:Asset,Liability,Equity,Revenue,Expense',
            'financial_period_id' => 'nullable|uuid|exists:financial_periods,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $query = \App\Models\Account::with(['accountType', 'accountGroup']);

            if ($request->account_type) {
                $query->whereHas('accountType', function ($q) use ($request) {
                    $q->where('name', $request->account_type);
                });
            }

            $accounts = $query->get()->map(function ($account) {
                return [
                    'id' => $account->id,
                    'name' => $account->name,
                    'code' => $account->code,
                    'account_type' => $account->accountType->name ?? null,
                    'account_group' => $account->accountGroup->name ?? null,
                ];
            });

            return response()->json([
                'success' => true,
                'message' => 'Account summary retrieved successfully',
                'data' => $accounts,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve account summary',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get financial metrics dashboard
     */
    public function getFinancialMetrics(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'financial_period_id' => 'nullable|uuid|exists:financial_periods,id',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $pnl = $this->financialReportingService->generateProfitAndLoss(
                $request->financial_period_id,
                $request->date_from,
                $request->date_to
            );

            $balanceSheet = $this->financialReportingService->generateBalanceSheet(
                $request->financial_period_id,
                $request->date_from,
                $request->date_to
            );

            $metrics = [
                'total_revenue' => $pnl['total_revenue'],
                'total_expenses' => $pnl['total_expenses'],
                'net_income' => $pnl['net_income'],
                'total_assets' => $balanceSheet['total_assets'],
                'total_liabilities' => $balanceSheet['total_liabilities'],
                'total_equity' => $balanceSheet['total_equity'],
                'profit_margin' => $pnl['total_revenue'] > 0 ? ($pnl['net_income'] / $pnl['total_revenue']) * 100 : 0,
                'debt_to_equity_ratio' => $balanceSheet['total_equity'] > 0 ? $balanceSheet['total_liabilities'] / $balanceSheet['total_equity'] : 0,
                'current_ratio' => $this->calculateCurrentRatio($balanceSheet),
            ];

            return response()->json([
                'success' => true,
                'message' => 'Financial metrics retrieved successfully',
                'data' => $metrics,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve financial metrics',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Calculate current ratio
     */
    private function calculateCurrentRatio($balanceSheet)
    {
        $currentAssets = $balanceSheet['assets']->where('account.accountType.name', 'Asset')->sum('balance');
        $currentLiabilities = $balanceSheet['liabilities']->where('account.accountType.name', 'Liability')->sum('balance');
        
        return $currentLiabilities > 0 ? $currentAssets / $currentLiabilities : 0;
    }
} 