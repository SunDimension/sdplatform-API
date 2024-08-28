<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class SalesReceipt extends Model
{
    use HasFactory,SoftDeletes ;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'customer_id',
        'branch_id',
        'sales_order_id',
        'sales_invoice_id',
        'store_id',
        'sales_receipt_number',
        'payment_mode_id',
        'quantity',
        'total_amount',
        'amount_paid',
        'receipt_date',
        
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'customer_id' => 'integer',
        'store_id'=>'integer',
        'branch_id' => 'integer',
        'sales_invoice_id' => 'integer',
        'sales_order_id' => 'integer',
        'payment_mode_id' => 'integer',
        
    ];

      protected static function boot()
{
    parent::boot();

    static::creating(function($salesreceipt) {
        // if (empty($createItem->batch_number)) {
        // dd($createItem);
            $salesreceipt->sales_receipt_number = static::generateSalesReceiptNumber();
        // }
    });
}

private static function generateSalesReceiptNumber()
{
        // dd('check');
    $prefix = 'HGV-SR';
    $timestamp = now()->format('YmdHis'); // Corrected method name
    $randomNumber = rand();

    return "{$prefix}-{$timestamp}-{$randomNumber}";
}

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function salesorder(): BelongsTo
    {
        return $this->belongsTo(SalesOrder::class);
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

}
