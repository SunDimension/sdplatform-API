<?php

namespace App\Services;

use App\Models\PeriodicFinancialReport;
use App\Models\FinancialPeriod;
use App\Models\Store;
use App\Models\Branch;
use App\Models\Region;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Carbon\Carbon;

class PeriodicFinancialReportService
{
    protected $financialReportingService;

    public function __construct(FinancialReportingService $financialReportingService)
    {
        $this->financialReportingService = $financialReportingService;
    }

    /**
     * Generate and store periodic financial reports
     */
    public function generateAndStoreReports($financialPeriodId, $storeId = null, $branchId = null, $regionId = null, $reportTypes = null)
    {
        if ($reportTypes === null) {
            $reportTypes = [
                PeriodicFinancialReport::REPORT_TYPE_PROFIT_LOSS,
                PeriodicFinancialReport::REPORT_TYPE_BALANCE_SHEET,
                PeriodicFinancialReport::REPORT_TYPE_TRIAL_BALANCE
            ];
        }

        $reports = [];
        $currentUser = Auth::user();

        foreach ($reportTypes as $reportType) {
            $report = $this->generateAndStoreReport($financialPeriodId, $reportType, $storeId, $branchId, $regionId, $currentUser);
            $reports[] = $report;
        }

        return $reports;
    }

    /**
     * Generate and store a single financial report
     */
    public function generateAndStoreReport($financialPeriodId, $reportType, $storeId = null, $branchId = null, $regionId = null, $user = null)
    {
        $user = $user ?? Auth::user();
        
        // Generate the report data
        $reportData = $this->generateReportData($financialPeriodId, $reportType, $storeId, $branchId, $regionId);
        
        // Calculate totals for trial balance
        $totalDebits = 0;
        $totalCredits = 0;
        $isBalanced = false;
        $difference = 0;

        if ($reportType === PeriodicFinancialReport::REPORT_TYPE_TRIAL_BALANCE) {
            $totalDebits = $reportData['total_debits'] ?? 0;
            $totalCredits = $reportData['total_credits'] ?? 0;
            $difference = abs($totalDebits - $totalCredits);
            $isBalanced = $difference < 0.01; // Allow for small rounding differences
        }

        // Store the report
        $report = PeriodicFinancialReport::create([
            'id' => Str::uuid(),
            'report_type' => $reportType,
            'financial_period_id' => $financialPeriodId,
            'store_id' => $storeId,
            'branch_id' => $branchId,
            'region_id' => $regionId,
            'report_data' => $reportData,
            'generated_at' => now(),
            'generated_by' => $user->id,
            'is_balanced' => $isBalanced,
            'total_debits' => $totalDebits,
            'total_credits' => $totalCredits,
            'difference' => $difference,
            'status' => PeriodicFinancialReport::STATUS_DRAFT,
        ]);

        return $report;
    }

    /**
     * Generate report data based on type and scope
     */
    protected function generateReportData($financialPeriodId, $reportType, $storeId = null, $branchId = null, $regionId = null)
    {
        // Get the financial period
        $period = FinancialPeriod::find($financialPeriodId);
        
        // Apply scope filters to transactions
        $this->applyScopeFilters($storeId, $branchId, $regionId);

        switch ($reportType) {
            case PeriodicFinancialReport::REPORT_TYPE_PROFIT_LOSS:
                return $this->financialReportingService->generateProfitAndLoss($financialPeriodId, null, null, $storeId, $branchId, $regionId);
                
            case PeriodicFinancialReport::REPORT_TYPE_BALANCE_SHEET:
                return $this->financialReportingService->generateBalanceSheet($financialPeriodId, null, null, $storeId, $branchId, $regionId);
                
            case PeriodicFinancialReport::REPORT_TYPE_TRIAL_BALANCE:
                return $this->financialReportingService->generateTrialBalance($financialPeriodId, null, null, $storeId, $branchId, $regionId);
                
            default:
                throw new \InvalidArgumentException("Invalid report type: {$reportType}");
        }
    }

    /**
     * Apply scope filters to transactions
     */
    protected function applyScopeFilters($storeId = null, $branchId = null, $regionId = null)
    {
        // This method would modify the FinancialReportingService to filter transactions
        // based on store, branch, and region. For now, we'll implement this in the
        // FinancialReportingService itself.
    }

    /**
     * Get stored reports with filters
     */
    public function getStoredReports($filters = [])
    {
        $query = PeriodicFinancialReport::with([
            'financialPeriod',
            'store',
            'branch',
            'region',
            'generatedBy'
        ]);

        // Apply filters
        if (isset($filters['report_type'])) {
            $query->ofType($filters['report_type']);
        }

        if (isset($filters['store_id'])) {
            $query->forStore($filters['store_id']);
        }

        if (isset($filters['branch_id'])) {
            $query->forBranch($filters['branch_id']);
        }

        if (isset($filters['region_id'])) {
            $query->forRegion($filters['region_id']);
        }

        if (isset($filters['financial_period_id'])) {
            $query->forPeriod($filters['financial_period_id']);
        }

        if (isset($filters['status'])) {
            $query->withStatus($filters['status']);
        }

        if (isset($filters['date_from'])) {
            $query->where('generated_at', '>=', $filters['date_from']);
        }

        if (isset($filters['date_to'])) {
            $query->where('generated_at', '<=', $filters['date_to']);
        }

        return $query->orderBy('generated_at', 'desc')->paginate($filters['per_page'] ?? 15);
    }

    /**
     * Get a specific stored report
     */
    public function getStoredReport($reportId)
    {
        return PeriodicFinancialReport::with([
            'financialPeriod',
            'store',
            'branch',
            'region',
            'generatedBy'
        ])->findOrFail($reportId);
    }

    /**
     * Update report status
     */
    public function updateReportStatus($reportId, $status, $notes = null)
    {
        $report = PeriodicFinancialReport::findOrFail($reportId);
        $report->update([
            'status' => $status,
            'notes' => $notes
        ]);

        return $report;
    }

    /**
     * Delete a stored report
     */
    public function deleteReport($reportId)
    {
        $report = PeriodicFinancialReport::findOrFail($reportId);
        return $report->delete();
    }

    /**
     * Generate reports for all stores in a branch
     */
    public function generateReportsForBranch($financialPeriodId, $branchId, $reportTypes = null)
    {
        $stores = Store::where('branch_id', $branchId)->get();
        $reports = [];

        foreach ($stores as $store) {
            $storeReports = $this->generateAndStoreReports($financialPeriodId, $store->id, $branchId, null, $reportTypes);
            $reports = array_merge($reports, $storeReports);
        }

        return $reports;
    }

    /**
     * Generate reports for all branches in a region
     */
    public function generateReportsForRegion($financialPeriodId, $regionId, $reportTypes = null)
    {
        $branches = Branch::where('region_id', $regionId)->get();
        $reports = [];

        foreach ($branches as $branch) {
            $branchReports = $this->generateReportsForBranch($financialPeriodId, $branch->id, $reportTypes);
            $reports = array_merge($reports, $branchReports);
        }

        return $reports;
    }

    /**
     * Get report summary statistics
     */
    public function getReportSummary($filters = [])
    {
        $query = PeriodicFinancialReport::query();

        // Apply filters
        if (isset($filters['report_type'])) {
            $query->ofType($filters['report_type']);
        }

        if (isset($filters['store_id'])) {
            $query->forStore($filters['store_id']);
        }

        if (isset($filters['branch_id'])) {
            $query->forBranch($filters['branch_id']);
        }

        if (isset($filters['region_id'])) {
            $query->forRegion($filters['region_id']);
        }

        if (isset($filters['financial_period_id'])) {
            $query->forPeriod($filters['financial_period_id']);
        }

        return [
            'total_reports' => $query->count(),
            'reports_by_type' => $query->selectRaw('report_type, count(*) as count')
                ->groupBy('report_type')
                ->pluck('count', 'report_type'),
            'reports_by_status' => $query->selectRaw('status, count(*) as count')
                ->groupBy('status')
                ->pluck('count', 'status'),
            'balanced_reports' => $query->where('is_balanced', true)->count(),
            'unbalanced_reports' => $query->where('is_balanced', false)->count(),
        ];
    }

    /**
     * Archive old reports
     */
    public function archiveOldReports($daysOld = 365)
    {
        $cutoffDate = Carbon::now()->subDays($daysOld);
        
        return PeriodicFinancialReport::where('generated_at', '<', $cutoffDate)
            ->where('status', PeriodicFinancialReport::STATUS_FINAL)
            ->update(['status' => PeriodicFinancialReport::STATUS_ARCHIVED]);
    }
} 