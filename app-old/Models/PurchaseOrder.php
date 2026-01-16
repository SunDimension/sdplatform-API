<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Concerns\Syncable;
use Illuminate\Support\Str;

class PurchaseOrder extends Model
{
    use HasFactory, HasUuid, Syncable;

    protected $primaryKey = 'po_id';
    public $incrementing = false;
    protected $keyType = 'string';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'po_number',
        'supplier_id',
        'pr_id',
        'order_date',
        'expected_delivery_date',
        'status',
        'created_by',
        'approved_by',
        'approved_at',
        'sync_id',
        'location_id',
        'sync_status',
        'sync_version',
        'last_synced_at',
        'last_sync_attempt_at',
        'sync_error',
        'po_id'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'po_id' => 'string',
        'supplier_id' => 'string',
        'pr_id' => 'string',
        'order_date' => 'datetime',
        'expected_delivery_date' => 'datetime',
        'status' => 'string',
        'created_by' => 'string',
        'approved_by' => 'string',
        'approved_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($purchaseOrder) {
            // REMOVE THIS BLOCK - You don't have an 'id' column
            // if (empty($purchaseOrder->id)) {
            //     $purchaseOrder->id = (string) Str::uuid();
            // }

            // CRITICAL FIX: Only generate po_number if it doesn't already exist
            if (empty($purchaseOrder->po_number)) {
                do {
                    $randomNumber = str_pad(mt_rand(0, 9999999), 7, '0', STR_PAD_LEFT);
                    $poNumber = 'HGV-PO-' . $randomNumber;
                } while (static::where('po_number', $poNumber)->exists());

                $purchaseOrder->po_number = $poNumber;
            }

            // Ensure po_id is set if not already
            if (empty($purchaseOrder->po_id)) {
                $purchaseOrder->po_id = (string) Str::uuid();
            }
        });
    }
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class, 'supplier_id',);
    }

    public function purchaseOrderItems(): HasMany
    {
        return $this->hasMany(PurchaseOrderItem::class, 'po_id');
    }

    public function createdByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function approvedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by', 'id');
    }
}
