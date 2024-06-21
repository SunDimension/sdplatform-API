<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurchaseReceivedDetail extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'new_purchased_received_id',
        'item_category_id',
        'item_id',
        'unit_price',
        'quantity',
        'unit_id',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'new_purchased_received_id' => 'integer',
        'item_category_id' => 'integer',
        'item_id' => 'integer',
        'unit_id' => 'integer',
    ];

    public function newPurchasedReceived(): BelongsTo
    {
        return $this->belongsTo(NewPurchaseReceived::class);
    }

    public function itemCategory(): BelongsTo
    {
        return $this->belongsTo(ItemCategory::class);
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(CreateItem::class);
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }
}
