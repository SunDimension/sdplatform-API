<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventoryAdjustment extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'item_id',
        'adjustment_type_id',
        'date',
        'reason_id',
        // 'branch_id',
        // 'warehouse_id',
        'description',
        // 'item_category_id',
        'cost_price',
        'selling_price',
        'quantity',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'item_id' => 'integer',
        'adjustment_type_id' => 'integer',
        'date' => 'timestamp',
        'reason_id' => 'integer',
        // 'branch_id' => 'integer',
        // 'warehouse_id' => 'integer',
        // 'item_category_id' => 'integer',
        'cost_price' => 'float',
        'selling_price' => 'float',
    ];

    public function item(): BelongsTo
    {
        return $this->belongsTo(CreateItem::class);
    }

    public function adjustmentType(): BelongsTo
    {
        return $this->belongsTo(AdjustmentType::class);
    }

    public function reason(): BelongsTo
    {
        return $this->belongsTo(Reason::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function itemCategory(): BelongsTo
    {
        return $this->belongsTo(ItemCategory::class);
    }
}
