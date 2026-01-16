<?php

// File: app/Models/PurchaseOrderItem.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Concerns\Syncable;
use Illuminate\Support\Str;

class PurchaseOrderItem extends Model
{
    use HasFactory, HasUuid, Syncable;

    protected $primaryKey = 'po_item_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'po_id',
        'product_id',
        'quantity_ordered',
        'unit_price',
        'amount',
        'sync_id',
        'location_id',
        'sync_status',
        'sync_version',
        'last_synced_at',
        'last_sync_attempt_at',
        'sync_error',
    ];

    protected $casts = [
        'po_item_id' => 'string',
        'po_id' => 'string',
        'product_id' => 'string',
        'quantity_ordered' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'amount' => 'decimal:2',
        'approved_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($purchaseOrderItem) {
            if (empty($purchaseOrderItem->po_item_id)) {
                $purchaseOrderItem->po_item_id = (string) Str::uuid();
            }
        });
    }

    /**
     * Get the purchase item cost (intermediate table)
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(PurchaseItemCost::class, 'product_id', 'id');
    }

    /**
     * Get the actual product/item details directly through nested relationship
     * This is a convenience accessor to get the CreateItem
     */
    public function createItem(): BelongsTo
    {
        return $this->belongsTo(CreateItem::class, 'product_id', 'id')
            ->join('purchase_item_costs', 'purchase_item_costs.id', '=', $this->product_id)
            ->join('create_items', 'create_items.id', '=', 'purchase_item_costs.product_id');
    }

    public function purchaseOrder(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrder::class, 'po_id', 'po_id');
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