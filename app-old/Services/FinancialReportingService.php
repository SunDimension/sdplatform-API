<?php

namespace App\Services;

use App\Models\Account;
use App\Models\AccountType;
use App\Models\Transaction;
use App\Models\TransactionJournalEntry;
use App\Models\TransactionJournalEntryDetail;
use App\Models\FinancialPeriod;
use App\Models\FinancialYear;
use App\Models\AccountOpeningBalance;
use App\Models\PeriodAccount;
use App\Models\PeriodAccountYear;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class FinancialReportingService
{
    /**
     * Generate Profit and Loss Statement
     */
    public function generateProfitAndLoss($financialPeriodId = null, $dateFrom = null, $dateTo = null, $storeId = null, $branchId = null, $regionId = null)
    {
        $period = $this->getFinancialPeriod($financialPeriodId, $dateFrom, $dateTo);
        
        // Get revenue accounts (AccountType = Revenue)
        $revenueAccounts = Account::whereHas('accountType', function ($query) {
            $query->where('name', 'Revenue');
        })->get();

        // Get expense accounts (AccountType = Expense)
        $expenseAccounts = Account::whereHas('accountType', function ($query) {
            $query->where('name', 'Expense');
        })->get();

        $revenues = $this->calculateAccountBalances($revenueAccounts, $period, $storeId, $branchId, $regionId);
        $expenses = $this->calculateAccountBalances($expenseAccounts, $period, $storeId, $branchId, $regionId);

        $totalRevenue = $revenues->sum('balance');
        $totalExpenses = $expenses->sum('balance');
        $netIncome = $totalRevenue - $totalExpenses;

        return [
            'period' => $period,
            'revenues' => $revenues,
            'expenses' => $expenses,
            'total_revenue' => $totalRevenue,
            'total_expenses' => $totalExpenses,
            'net_income' => $netIncome,
            'generated_at' => now(),
            'scope' => [
                'store_id' => $storeId,
                'branch_id' => $branchId,
                'region_id' => $regionId,
            ],
        ];
    }

    /**
     * Generate Balance Sheet
     */
    public function generateBalanceSheet($financialPeriodId = null, $dateFrom = null, $dateTo = null, $storeId = null, $branchId = null, $regionId = null)
    {
        $period = $this->getFinancialPeriod($financialPeriodId, $dateFrom, $dateTo);
        
        // Get asset accounts
        $assetAccounts = Account::whereHas('accountType', function ($query) {
            $query->where('name', 'Asset');
        })->get();

        // Get liability accounts
        $liabilityAccounts = Account::whereHas('accountType', function ($query) {
            $query->where('name', 'Liability');
        })->get();

        // Get equity accounts
        $equityAccounts = Account::whereHas('accountType', function ($query) {
            $query->where('name', 'Equity');
        })->get();

        $assets = $this->calculateAccountBalances($assetAccounts, $period, $storeId, $branchId, $regionId);
        $liabilities = $this->calculateAccountBalances($liabilityAccounts, $period, $storeId, $branchId, $regionId);
        $equity = $this->calculateAccountBalances($equityAccounts, $period, $storeId, $branchId, $regionId);

        $totalAssets = $assets->sum('balance');
        $totalLiabilities = $liabilities->sum('balance');
        $totalEquity = $equity->sum('balance');
        $totalLiabilitiesAndEquity = $totalLiabilities + $totalEquity;

        return [
            'period' => $period,
            'assets' => $assets,
            'liabilities' => $liabilities,
            'equity' => $equity,
            'total_assets' => $totalAssets,
            'total_liabilities' => $totalLiabilities,
            'total_equity' => $totalEquity,
            'total_liabilities_and_equity' => $totalLiabilitiesAndEquity,
            'generated_at' => now(),
            'scope' => [
                'store_id' => $storeId,
                'branch_id' => $branchId,
                'region_id' => $regionId,
            ],
        ];
    }

    /**
     * Generate Trial Balance
     */
    public function generateTrialBalance($financialPeriodId = null, $dateFrom = null, $dateTo = null, $storeId = null, $branchId = null, $regionId = null)
    {
        $period = $this->getFinancialPeriod($financialPeriodId, $dateFrom, $dateTo);
        
        // Get all accounts
        $accounts = Account::with(['accountType', 'accountGroup'])->get();
        
        $trialBalance = collect();
        $totalDebits = 0;
        $totalCredits = 0;

        foreach ($accounts as $account) {
            $openingBalance = $this->getOpeningBalance($account, $period);
            $periodTransactions = $this->getPeriodTransactions($account, $period, $storeId, $branchId, $regionId);
            
            $debitTotal = $periodTransactions->sum('debit');
            $creditTotal = $periodTransactions->sum('credit');
            
            // Calculate balance based on account type
            $balance = $this->calculateBalanceByAccountType($account, $openingBalance, $debitTotal, $creditTotal);
            
            // Determine if this is a debit or credit balance for trial balance
            $accountType = $account->accountType->name;
            $isDebitBalance = in_array($accountType, ['Asset', 'Expense']);
            
            $trialBalance->push([
                'account' => [
                    'id' => $account->id,
                    'name' => $account->name,
                    'code' => $account->code,
                    'account_type' => $accountType,
                    'account_group' => $account->accountGroup->name ?? null,
                ],
                'opening_balance' => $openingBalance,
                'debit_total' => $debitTotal,
                'credit_total' => $creditTotal,
                'balance' => $balance,
                'is_debit_balance' => $isDebitBalance,
                'debit_balance' => $isDebitBalance ? $balance : 0,
                'credit_balance' => $isDebitBalance ? 0 : $balance,
            ]);
            
            // Add to totals
            if ($isDebitBalance) {
                $totalDebits += $balance;
            } else {
                $totalCredits += $balance;
            }
        }

        // Sort by account code for better presentation
        $trialBalance = $trialBalance->sortBy('account.code');

        return [
            'period' => $period,
            'trial_balance' => $trialBalance,
            'total_debits' => $totalDebits,
            'total_credits' => $totalCredits,
            'difference' => $totalDebits - $totalCredits,
            'is_balanced' => abs($totalDebits - $totalCredits) < 0.01, // Allow for small rounding differences
            'generated_at' => now(),
            'scope' => [
                'store_id' => $storeId,
                'branch_id' => $branchId,
                'region_id' => $regionId,
            ],
        ];
    }

    /**
     * Generate detailed trial balance with account breakdown
     */
    public function generateDetailedTrialBalance($financialPeriodId = null, $dateFrom = null, $dateTo = null)
    {
        $trialBalance = $this->generateTrialBalance($financialPeriodId, $dateFrom, $dateTo);
        
        // Group by account type for better organization
        $groupedTrialBalance = $trialBalance['trial_balance']->groupBy('account.account_type');
        
        $detailedTrialBalance = [];
        foreach ($groupedTrialBalance as $accountType => $accounts) {
            $typeTotalDebits = $accounts->sum('debit_balance');
            $typeTotalCredits = $accounts->sum('credit_balance');
            
            $detailedTrialBalance[$accountType] = [
                'accounts' => $accounts,
                'total_debits' => $typeTotalDebits,
                'total_credits' => $typeTotalCredits,
                'net_balance' => $typeTotalDebits - $typeTotalCredits,
            ];
        }
        
        $trialBalance['detailed_breakdown'] = $detailedTrialBalance;
        
        return $trialBalance;
    }

    /**
     * Calculate account balances for a given period
     */
    private function calculateAccountBalances($accounts, $period, $storeId = null, $branchId = null, $regionId = null)
    {
        $balances = collect();

        foreach ($accounts as $account) {
            $openingBalance = $this->getOpeningBalance($account, $period);
            $periodTransactions = $this->getPeriodTransactions($account, $period, $storeId, $branchId, $regionId);
            
            $debitTotal = $periodTransactions->sum('debit');
            $creditTotal = $periodTransactions->sum('credit');
            
            // Calculate balance based on account type
            $balance = $this->calculateBalanceByAccountType($account, $openingBalance, $debitTotal, $creditTotal);
            
            $balances->push([
                'account' => $account,
                'opening_balance' => $openingBalance,
                'debit_total' => $debitTotal,
                'credit_total' => $creditTotal,
                'balance' => $balance,
                'transactions' => $periodTransactions,
            ]);
        }

        return $balances;
    }

    /**
     * Get opening balance for an account
     */
    private function getOpeningBalance($account, $period)
    {
        // Try to get from AccountOpeningBalance first
        $openingBalance = AccountOpeningBalance::where('account_id', $account->id)
            ->where('financial_period_id', $period['id'])
            ->first();

        if ($openingBalance) {
            return $openingBalance->amount;
        }

        // If no opening balance, calculate from previous periods
        $previousTransactions = Transaction::where('account_id', $account->id)
            ->where('transaction_date', '<', $period['date_from'])
            ->get();

        $debitTotal = $previousTransactions->sum('debit');
        $creditTotal = $previousTransactions->sum('credit');
        
        return $this->calculateBalanceByAccountType($account, 0, $debitTotal, $creditTotal);
    }

    /**
     * Get transactions for a specific period
     */
    private function getPeriodTransactions($account, $period, $storeId = null, $branchId = null, $regionId = null)
    {
        $query = Transaction::where('account_id', $account->id)
            ->whereBetween('transaction_date', [$period['date_from'], $period['date_to']]);

        // Apply scope filters if provided
        if ($storeId) {
            $query->where('store_id', $storeId);
        }

        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        if ($regionId) {
            $query->where('region_id', $regionId);
        }

        return $query->orderBy('transaction_date')->get();
    }

    /**
     * Calculate balance based on account type
     */
    private function calculateBalanceByAccountType($account, $openingBalance, $debitTotal, $creditTotal)
    {
        $accountType = $account->accountType->name;
        
        switch ($accountType) {
            case 'Asset':
            case 'Expense':
                // Assets and Expenses: Debit increases, Credit decreases
                return $openingBalance + $debitTotal - $creditTotal;
                
            case 'Liability':
            case 'Equity':
            case 'Revenue':
                // Liabilities, Equity, and Revenue: Credit increases, Debit decreases
                return $openingBalance + $creditTotal - $debitTotal;
                
            default:
                return $openingBalance + $debitTotal - $creditTotal;
        }
    }

    /**
     * Get financial period details
     */
    private function getFinancialPeriod($financialPeriodId, $dateFrom, $dateTo)
    {
        if ($financialPeriodId) {
            $period = FinancialPeriod::find($financialPeriodId);
            if ($period) {
                return [
                    'id' => $period->id,
                    'name' => $period->name,
                    'date_from' => $period->date_from,
                    'date_to' => $period->date_to,
                    'financial_year' => $period->financialYear->name ?? null,
                ];
            }
        }

        // Use custom date range
        $dateFrom = $dateFrom ? Carbon::parse($dateFrom) : Carbon::now()->startOfMonth();
        $dateTo = $dateTo ? Carbon::parse($dateTo) : Carbon::now()->endOfMonth();

        return [
            'id' => null,
            'name' => 'Custom Period',
            'date_from' => $dateFrom->format('Y-m-d'),
            'date_to' => $dateTo->format('Y-m-d'),
            'financial_year' => null,
        ];
    }

    /**
     * Generate comparative financial statements
     */
    public function generateComparativeStatements($currentPeriodId, $previousPeriodId)
    {
        $currentPnl = $this->generateProfitAndLoss($currentPeriodId);
        $previousPnl = $this->generateProfitAndLoss($previousPeriodId);
        
        $currentBalanceSheet = $this->generateBalanceSheet($currentPeriodId);
        $previousBalanceSheet = $this->generateBalanceSheet($previousPeriodId);

        return [
            'profit_and_loss' => [
                'current' => $currentPnl,
                'previous' => $previousPnl,
                'variances' => $this->calculateVariances($currentPnl, $previousPnl),
            ],
            'balance_sheet' => [
                'current' => $currentBalanceSheet,
                'previous' => $previousBalanceSheet,
                'variances' => $this->calculateVariances($currentBalanceSheet, $previousBalanceSheet),
            ],
        ];
    }

    /**
     * Calculate variances between periods
     */
    private function calculateVariances($current, $previous)
    {
        $variances = [];
        
        // Calculate percentage changes for key metrics
        $metrics = ['total_revenue', 'total_expenses', 'net_income', 'total_assets', 'total_liabilities', 'total_equity'];
        
        foreach ($metrics as $metric) {
            if (isset($current[$metric]) && isset($previous[$metric])) {
                $currentValue = $current[$metric];
                $previousValue = $previous[$metric];
                
                $absoluteChange = $currentValue - $previousValue;
                $percentageChange = $previousValue != 0 ? ($absoluteChange / $previousValue) * 100 : 0;
                
                $variances[$metric] = [
                    'current' => $currentValue,
                    'previous' => $previousValue,
                    'absolute_change' => $absoluteChange,
                    'percentage_change' => $percentageChange,
                ];
            }
        }
        
        return $variances;
    }

    /**
     * Export financial statements to different formats
     */
    public function exportStatement($statement, $format = 'json')
    {
        switch ($format) {
            case 'json':
                return json_encode($statement, JSON_PRETTY_PRINT);
                
            case 'csv':
                return $this->convertToCsv($statement);
                
            case 'pdf':
                // Implementation for PDF export would go here
                return $statement;
                
            default:
                return $statement;
        }
    }

    /**
     * Convert statement to CSV format
     */
    private function convertToCsv($statement)
    {
        // Implementation for CSV conversion
        // This would convert the statement data to CSV format
        return $statement;
    }
} 