<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NewPurchasOrder extends Model
{
    use HasFactory, HasUuids;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'item_category_id',
        'item_id',
        'vendor_id',
        'branch_id',
        'payment_mode_id',
        'purchase_order_number',
        'purchase_amount',
        'purchase_date',
        'expected_delivery_date',
        'payment_type_id',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'item_category_id' => 'integer',
        'item_id' => 'integer',
        'vendor_id' => 'integer',
        'branch_id' => 'integer',
        'payment_mode_id' => 'integer',
        'purchase_date' => 'timestamp',
        'expected_delivery_date' => 'date',
        'payment_type_id' => 'integer',
    ];

    public function itemCategory(): BelongsTo
    {
        return $this->belongsTo(ItemCategory::class);
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(CreateItem::class);
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function paymentMode(): BelongsTo
    {
        return $this->belongsTo(PaymentMode::class);
    }

    public function paymentType(): BelongsTo
    {
        return $this->belongsTo(PaymentType::class);
    }
}
