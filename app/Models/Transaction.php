<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'financial_period_id',
        'transaction_date',
        'transcode',
        'transtype',
        'naration',
        'debit',
        'credit',
        'amount',
        'warehouse_id',
        'account_no',
        'account_id',
        'created_by',
        'modified_by',
        'deleted_by',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'transaction_date' => 'date',
        'debit' => 'double',
        'credit' => 'double',
        'amount' => 'double',
        'warehouse_id' => 'integer',
        'created_by' => 'integer',
        'modified_by' => 'integer',
        'deleted_by' => 'integer',
    ];

    public function financialPeriod(): BelongsTo
    {
        return $this->belongsTo(FinancialPeriod::class);
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function modifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function deletedBy(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
