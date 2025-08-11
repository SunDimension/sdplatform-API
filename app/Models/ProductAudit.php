<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductAudit extends Model
{
    use HasFactory;

    protected $fillable = [
        'action_type',
        'product_id',
        'user_id',
        'store_id',
        'quantity_change',
        'previous_quantity',
        'new_quantity',
        'price_change',
        'previous_price',
        'new_price',
        'reference_type',
        'reference_id',
        'notes'
    ];

    protected $casts = [
        'quantity_change' => 'decimal:2',
        'previous_quantity' => 'decimal:2',
        'new_quantity' => 'decimal:2',
        'price_change' => 'decimal:2',
        'previous_price' => 'decimal:2',
        'new_price' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Action types constants
    const ACTION_CREATED = 'created';
    const ACTION_UPDATED = 'updated';
    const ACTION_DELETED = 'deleted';
    const ACTION_RELEASED = 'released';
    const ACTION_PENDING_RECEIPT = 'pending_receipt';
    const ACTION_RECEIVED = 'received';
    const ACTION_RECEIPT_CANCELLED = 'receipt_cancelled';
    const ACTION_SOLD = 'sold';
    const ACTION_RETURNED = 'returned';
    const ACTION_PRICE_CHANGE = 'price_change';
    const ACTION_STOCK_ADJUSTMENT = 'stock_adjustment';
    const ACTION_TRANSFER_OUT_PENDING = 'transfer_out_pending';
    const ACTION_TRANSFER_IN_PENDING = 'transfer_in_pending';
    const ACTION_TRANSFER_OUT = 'transfer_out';
    const ACTION_TRANSFER_IN = 'transfer_in';
    const ACTION_TRANSFER_CANCELLED = 'transfer_cancelled';

    /**
     * Get the user who performed the action
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the product that was audited
     */
    public function product()
    {
        return $this->belongsTo(CreateItem::class, 'product_id');
    }

    /**
     * Get the store where the action occurred
     */
    public function store()
    {
        return $this->belongsTo(Store::class, 'store_id');
    }

    /**
     * Scope a query to filter by action type
     */
    public function scopeActionType($query, $type)
    {
        return $query->where('action_type', $type);
    }

    /**
     * Scope a query to filter by date range
     */
    public function scopeDateRange($query, $from, $to)
    {
        return $query->whereBetween('created_at', [$from, $to]);
    }

    /**
     * Scope a query to filter by product
     */
    public function scopeForProduct($query, $productId)
    {
        return $query->where('product_id', $productId);
    }

    /**
     * Scope a query to filter by store
     */
    public function scopeForStore($query, $storeId)
    {
        return $query->where('store_id', $storeId);
    }

    /**
     * Helper method to log product additions to stores
     */
    public static function logProductAddedToStore($productId, $storeId, $quantity, $userId, $referenceType = null, $referenceId = null)
    {
        return self::create([
            'action_type' => self::ACTION_CREATED,
            'product_id' => $productId,
            'store_id' => $storeId,
            'user_id' => $userId,
            'quantity_change' => $quantity,
            'new_quantity' => $quantity, // For new items, previous quantity would be 0
            'reference_type' => $referenceType,
            'reference_id' => $referenceId,
            'notes' => 'Product added to store inventory'
        ]);
    }

    // Polymorphic relationship to reference various models
    public function reference()
    {
        return $this->morphTo(__FUNCTION__, 'reference_type', 'reference_id');
    }
}
