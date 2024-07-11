<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Sale extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'customer_id',
        'product_id',
        'branch_id',
        'warehouse_id',
        'quantity',
        'price',
        'discount_id',
        'discount',
        'sales_order_number',
        'total_amount',
        'amount_paid',
        'balance_amount',
        'payment_mode',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'customer_id' => 'integer',
        'product_id' => 'integer',
        'branch_id' => 'integer',
        'warehouse_id' => 'integer',
        'quantity' => 'float',
        'price' => 'float',
        'discount_id' => 'integer',
        'discount' => 'float',
        'amount_paid' => 'float',
        'balance_amount' => 'float',
        'payment_mode' => 'integer',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(CreateItem::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function discount(): BelongsTo
    {
        return $this->belongsTo(Discount::class);
    }

    public function paymentMode(): BelongsTo
    {
        return $this->belongsTo(PaymentMode::class);
    }
}
