<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class CashierRemittance extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        // 'expense_name',
        'branch_id',
        'amount',
        'user_id',
        'store_id',
        'date',
        'approved_by',
        'approval_date',
        'discrepancy_amount',
        'cash_discrepancy_id',
        'approval_comment',
        'status'

    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'branch_id' => 'integer',
        'user_id' => 'integer',
        'store_id' => 'integer',
        'approved_by' => 'integer',
        'cash_discrepancy_id' => 'integer'
    ];
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }
    public function store()
    {
        return $this->belongsTo(Store::class,'store_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function approvedby()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function discrepancy()
    {
        return $this->belongsTo(CashDiscrepancy::class,'cash_discrepancy_id');
    }
}
