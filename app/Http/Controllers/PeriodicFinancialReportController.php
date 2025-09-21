<?php

namespace App\Http\Controllers;

use App\Models\PeriodicFinancialReport;
use App\Models\FinancialPeriod;
use App\Models\Store;
use App\Models\Branch;
use App\Models\Region;
use App\Services\PeriodicFinancialReportService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class PeriodicFinancialReportController extends Controller
{
    protected $periodicFinancialReportService;

    public function __construct(PeriodicFinancialReportService $periodicFinancialReportService)
    {
        $this->periodicFinancialReportService = $periodicFinancialReportService;
    }

    /**
     * Generate and store periodic financial reports
     */
    public function generateReports(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'financial_period_id' => 'required|uuid|exists:financial_periods,id',
            'store_id' => 'nullable|string|exists:stores,id',
            'branch_id' => 'nullable|string|exists:branches,id',
            'region_id' => 'nullable|integer|exists:regions,id',
            'report_types' => 'nullable|array',
            'report_types.*' => 'in:profit_loss,balance_sheet,trial_balance',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $reports = $this->periodicFinancialReportService->generateAndStoreReports(
                $request->financial_period_id,
                $request->store_id,
                $request->branch_id,
                $request->region_id,
                $request->report_types
            );

            return response()->json([
                'success' => true,
                'message' => 'Periodic financial reports generated and stored successfully',
                'data' => [
                    'reports' => $reports,
                    'count' => count($reports),
                ],
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate periodic financial reports',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get stored reports with filters
     */
    public function getReports(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'report_type' => 'nullable|in:profit_loss,balance_sheet,trial_balance',
            'store_id' => 'nullable|string|exists:stores,id',
            'branch_id' => 'nullable|string|exists:branches,id',
            'region_id' => 'nullable|integer|exists:regions,id',
            'financial_period_id' => 'nullable|uuid|exists:financial_periods,id',
            'status' => 'nullable|in:draft,final,archived',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
            'per_page' => 'nullable|integer|min:1|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $filters = $request->only([
                'report_type', 'store_id', 'branch_id', 'region_id',
                'financial_period_id', 'status', 'date_from', 'date_to', 'per_page'
            ]);

            $reports = $this->periodicFinancialReportService->getStoredReports($filters);

            return response()->json([
                'success' => true,
                'message' => 'Periodic financial reports retrieved successfully',
                'data' => $reports,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve periodic financial reports',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get a specific stored report
     */
    public function getReport($reportId): JsonResponse
    {
        try {
            $report = $this->periodicFinancialReportService->getStoredReport($reportId);

            return response()->json([
                'success' => true,
                'message' => 'Periodic financial report retrieved successfully',
                'data' => $report,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve periodic financial report',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update report status
     */
    public function updateReportStatus(Request $request, $reportId): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:draft,final,archived',
            'notes' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $report = $this->periodicFinancialReportService->updateReportStatus(
                $reportId,
                $request->status,
                $request->notes
            );

            return response()->json([
                'success' => true,
                'message' => 'Report status updated successfully',
                'data' => $report,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update report status',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete a stored report
     */
    public function deleteReport($reportId): JsonResponse
    {
        try {
            $this->periodicFinancialReportService->deleteReport($reportId);

            return response()->json([
                'success' => true,
                'message' => 'Report deleted successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete report',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Generate reports for all stores in a branch
     */
    public function generateReportsForBranch(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'financial_period_id' => 'required|uuid|exists:financial_periods,id',
            'branch_id' => 'required|string|exists:branches,id',
            'report_types' => 'nullable|array',
            'report_types.*' => 'in:profit_loss,balance_sheet,trial_balance',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $reports = $this->periodicFinancialReportService->generateReportsForBranch(
                $request->financial_period_id,
                $request->branch_id,
                $request->report_types
            );

            return response()->json([
                'success' => true,
                'message' => 'Reports generated for all stores in branch successfully',
                'data' => [
                    'reports' => $reports,
                    'count' => count($reports),
                ],
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate reports for branch',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Generate reports for all branches in a region
     */
    public function generateReportsForRegion(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'financial_period_id' => 'required|uuid|exists:financial_periods,id',
            'region_id' => 'required|integer|exists:regions,id',
            'report_types' => 'nullable|array',
            'report_types.*' => 'in:profit_loss,balance_sheet,trial_balance',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $reports = $this->periodicFinancialReportService->generateReportsForRegion(
                $request->financial_period_id,
                $request->region_id,
                $request->report_types
            );

            return response()->json([
                'success' => true,
                'message' => 'Reports generated for all branches in region successfully',
                'data' => [
                    'reports' => $reports,
                    'count' => count($reports),
                ],
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate reports for region',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get report summary statistics
     */
    public function getReportSummary(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'report_type' => 'nullable|in:profit_loss,balance_sheet,trial_balance',
            'store_id' => 'nullable|string|exists:stores,id',
            'branch_id' => 'nullable|string|exists:branches,id',
            'region_id' => 'nullable|integer|exists:regions,id',
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
            $filters = $request->only([
                'report_type', 'store_id', 'branch_id', 'region_id', 'financial_period_id'
            ]);

            $summary = $this->periodicFinancialReportService->getReportSummary($filters);

            return response()->json([
                'success' => true,
                'message' => 'Report summary retrieved successfully',
                'data' => $summary,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve report summary',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Archive old reports
     */
    public function archiveOldReports(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'days_old' => 'nullable|integer|min:30|max:3650', // 30 days to 10 years
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $daysOld = $request->input('days_old', 365);
            $archivedCount = $this->periodicFinancialReportService->archiveOldReports($daysOld);

            return response()->json([
                'success' => true,
                'message' => 'Old reports archived successfully',
                'data' => [
                    'archived_count' => $archivedCount,
                    'days_old' => $daysOld,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to archive old reports',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get available stores, branches, and regions for filtering
     */
    public function getFilterOptions(): JsonResponse
    {
        try {
            $stores = Store::select('id', 'name')->get();
            $branches = Branch::select('id', 'name')->get();
            $regions = Region::select('id', 'name')->get();
            $financialPeriods = FinancialPeriod::select('id', 'name', 'date_from', 'date_to')->get();

            return response()->json([
                'success' => true,
                'message' => 'Filter options retrieved successfully',
                'data' => [
                    'stores' => $stores,
                    'branches' => $branches,
                    'regions' => $regions,
                    'financial_periods' => $financialPeriods,
                    'report_types' => PeriodicFinancialReport::getReportTypes(),
                    'statuses' => PeriodicFinancialReport::getStatuses(),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve filter options',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
} 