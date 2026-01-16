<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class PeriodicFinancialReport extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'report_type', // 'profit_loss', 'balance_sheet', 'trial_balance'
        'financial_period_id',
        'store_id',
        'branch_id',
        'region_id',
        'report_data', // JSON data of the report
        'generated_at',
        'generated_by',
        'is_balanced',
        'total_debits',
        'total_credits',
        'difference',
        'status', // 'draft', 'final', 'archived'
        'notes',
    ];

    protected $casts = [
        'id' => 'string',
        'financial_period_id' => 'string',
        'store_id' => 'integer',
        'branch_id' => 'integer',
        'region_id' => 'integer',
        'report_data' => 'array',
        'generated_at' => 'datetime',
        'generated_by' => 'integer',
        'is_balanced' => 'boolean',
        'total_debits' => 'decimal:2',
        'total_credits' => 'decimal:2',
        'difference' => 'decimal:2',
    ];

    // Report types
    const REPORT_TYPE_PROFIT_LOSS = 'profit_loss';
    const REPORT_TYPE_BALANCE_SHEET = 'balance_sheet';
    const REPORT_TYPE_TRIAL_BALANCE = 'trial_balance';

    // Status types
    const STATUS_DRAFT = 'draft';
    const STATUS_FINAL = 'final';
    const STATUS_ARCHIVED = 'archived';

    public function financialPeriod(): BelongsTo
    {
        return $this->belongsTo(FinancialPeriod::class);
    }

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class);
    }

    public function generatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'generated_by');
    }

    /**
     * Scope for filtering by report type
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('report_type', $type);
    }

    /**
     * Scope for filtering by store
     */
    public function scopeForStore($query, $storeId)
    {
        return $query->where('store_id', $storeId);
    }

    /**
     * Scope for filtering by branch
     */
    public function scopeForBranch($query, $branchId)
    {
        return $query->where('branch_id', $branchId);
    }

    /**
     * Scope for filtering by region
     */
    public function scopeForRegion($query, $regionId)
    {
        return $query->where('region_id', $regionId);
    }

    /**
     * Scope for filtering by status
     */
    public function scopeWithStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope for filtering by financial period
     */
    public function scopeForPeriod($query, $periodId)
    {
        return $query->where('financial_period_id', $periodId);
    }

    /**
     * Get report type options
     */
    public static function getReportTypes()
    {
        return [
            self::REPORT_TYPE_PROFIT_LOSS => 'Profit and Loss',
            self::REPORT_TYPE_BALANCE_SHEET => 'Balance Sheet',
            self::REPORT_TYPE_TRIAL_BALANCE => 'Trial Balance',
        ];
    }

    /**
     * Get status options
     */
    public static function getStatuses()
    {
        return [
            self::STATUS_DRAFT => 'Draft',
            self::STATUS_FINAL => 'Final',
            self::STATUS_ARCHIVED => 'Archived',
        ];
    }
} 