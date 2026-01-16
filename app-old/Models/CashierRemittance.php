<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasUuid;
use Illuminate\Support\Str;
use App\Models\Concerns\Syncable;

class CashierRemittance extends Model
{
    use HasFactory, HasUuid, Syncable;

    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

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
        'status',
        'sync_id',
        'location_id',
        'sync_status',
        'sync_version',
        'last_synced_at',
        'last_sync_attempt_at',
        'sync_error',
        'id'

    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'string',
        'branch_id' => 'string',
        'user_id' => 'string',
        'store_id' => 'string',
        'approved_by' => 'string',
        'cash_discrepancy_id' => 'string'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            // Generate UUID for id if not set
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }
        });
    }
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }
    public function store()
    {
        return $this->belongsTo(Store::class, 'store_id');
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
        return $this->belongsTo(CashDiscrepancy::class, 'cash_discrepancy_id');
    }

    public function getCashDiscrepancyNameAttribute()
    {
        return $this->discrepancy ? $this->discrepancy->name : null;
    }
}
