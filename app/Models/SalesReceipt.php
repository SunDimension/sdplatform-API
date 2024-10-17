<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class SalesReceipt extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'customer_id',
        'branch_id',
        'store_id',
        'sales_receipt_number',
        'payment_type',
        'total_amount',
        'amount_paid',
        'receipt_date',
        'sales_order_id',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
    **/

    protected $casts = [
        'id' => 'integer',
        'customer_id' => 'integer',
        'store_id' => 'integer',
        'branch_id' => 'integer',
        'item_sold_id' => 'integer',
        'sales_order_id' => 'integer',
    ];

   protected static function boot()
{
    parent::boot();

    // Automatically generate sales_order_number when a new SalesOrder is created
    static::creating(function ($salesReceipt) {
        $randomNumber = str_pad(mt_rand(0, 9999999), 7, '0', STR_PAD_LEFT); // Generates a random 7-digit number
        $salesReceipt->sales_receipt_number = 'HGV-SR-' . $randomNumber;
    });
}

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function salesorder()
    {
        return $this->belongsTo(SalesOrder::class, "sales_order_id");
    }

    public function salesinvoice(): BelongsTo
    {
        return $this->belongsTo(SalesInvoice::class);
    }

    public function paymentMode(): BelongsTo
    {
        return $this->belongsTo(PaymentMode::class);
    }

    public function stores(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    // public function itemSold()
    // {
    //     return $this->hasMany(ItemSold::class);
    // }
}
