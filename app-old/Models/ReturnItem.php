<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\HasUuid;
use App\Models\Concerns\Syncable;
class ReturnItem extends Model
{
    use HasFactory, SoftDeletes, HasUuid, Syncable;

    protected $fillable = [

        'release_id',
        'sales_receipt_id',
        'customer_id',
        'branch_id',
        'store_id',
        'return_date',
        'notes',
        'created_by',
        'approved_by',
        'approved_at',
        
        'return_status',
        'approval_comment',
          'id',
        'sync_id',
        'location_id',
        'sync_status',
        'sync_version',
        'last_synced_at',
        'last_sync_attempt_at',
        'sync_error',

    ];

    protected $casts = [
        'id' => 'string',
        'release_id' => 'string',
        'sales_receipt_id' => 'string',
        'branch_id' => 'string',
        'customer_id' => 'string',
        'store_id' => 'string',
        'created_by' => 'string',
        'approved_by' => 'string',

    ];


    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }
    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }
    public function salesReceipt(): BelongsTo
    {
        return $this->belongsTo(SalesReceipt::class, 'sales_receipt_id');
    }

    public function release(): BelongsTo
    {
        return $this->belongsTo(Release::class);
    }

    public function returnDetails(): HasMany
    {
        return $this->hasMany(ReturnDetails::class, 'return_id');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
