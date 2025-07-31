<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class StoreTransferOrder extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'order_number',
        'transfer_date',
        'source_branch_id',
        'source_store_id',
        'destination_branch_id',
        'destination_store_id',
        'approval_stage_id',
        'source_status',
        'source_approved_by',
        'source_approval_date',
        'destination_status',
        'destination_approved_by',
        'destination_approval_date',
        'branch_approved_by',
        'branch_approval_date',
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
        'transfer_date' => 'datetime',
        'source_branch_id' => 'integer',
        'source_store_id' => 'integer',
        'destination_branch_id' => 'integer',
        'destination_store_id' => 'integer',
        'approval_stage_id' => 'integer',
        'source_approval_date' => 'timestamp',
        'destination_approval_date' => 'timestamp',
        'branch_approval_date' => 'timestamp',
        'created_by' => 'integer',
        'modified_by' => 'integer',
        'deleted_by' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($order) {
            do {
                $randomNumber = str_pad(mt_rand(0, 9999999), 7, '0', STR_PAD_LEFT);
                $OrderNumber = 'HGV-ST-' . $randomNumber;
            } while (static::where('order_number', $OrderNumber)->exists());
            $order->order_number = $OrderNumber;
        });
    }

    public function items(): HasMany
    {
        return $this->hasMany(StoreTransferItem::class, 'transfer_order_id');
    }

    public function sourceBranch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'destination_branch_id');
    }

    public function sourceStore(): BelongsTo
    {
        return $this->belongsTo(Store::class, 'source_store_id');
    }

    public function destinationBranch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'destination_branch_id');
    }

    public function destinationStore(): BelongsTo
    {
        return $this->belongsTo(Store::class, 'destination_store_id');
    }

    public function approvalStage(): BelongsTo
    {
        return $this->belongsTo(ApprovalStage::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, "created_by");
    }

    public function modifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'modified_by');
    }

    public function deletedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }
}
