<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StoreItem extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'store_items';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'item_category_id',
        'create_item_id',
        'unit_id',
        'quantity',
        'quantity_holding',
        'cost_price',
        'selling_price',
        'reorder_level',
        'discount',
        'user_id',
        'branch_id',
        'store_id',
        'open_stock',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'quantity' => 'float',
        'quantity_holding' => 'float',
        'cost_price' => 'float',
        'selling_price' => 'float',
        'discount' => 'integer',
        'open_stock' => 'float',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the item category that owns the store item.
     */
    public function itemCategory(): BelongsTo
    {
        return $this->belongsTo(ItemCategory::class, 'item_category_id');
    }

    /**
     * Get the create item that owns the store item.
     */
    public function createItem(): BelongsTo
    {
        return $this->belongsTo(CreateItem::class, 'create_item_id');
    }

    /**
     * Get the unit that owns the store item.
     */
    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }

    /**
     * Get the user that owns the store item.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the branch that owns the store item.
     */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    /**
     * Get the store that owns the store item.
     */
    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class, 'store_id');
    }

    /**
     * Get the available quantity (quantity - quantity_holding).
     */
    public function getAvailableQuantityAttribute(): float
    {
        return $this->quantity - $this->quantity_holding;
    }

    /**
     * Check if the item needs reordering.
     */
    public function needsReorder(): bool
    {
        return $this->available_quantity <= (float) $this->reorder_level;
    }

    /**
     * Get the profit margin.
     */
    public function getProfitMarginAttribute(): float
    {
        if ($this->cost_price > 0) {
            return (($this->selling_price - $this->cost_price) / $this->cost_price) * 100;
        }
        return 0;
    }

    /**
     * Get the selling price with discount applied.
     */
    public function getDiscountedPriceAttribute(): float
    {
        if ($this->discount && $this->discount > 0) {
            return $this->selling_price - ($this->selling_price * ($this->discount / 100));
        }
        return $this->selling_price;
    }
}
