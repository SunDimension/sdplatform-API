<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SalesInvoice extends Model
{
    use HasFactory,SoftDeletes;


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'sales_order_number',
        'customer_id',
        'branch_id',
        'store_id',
        'credit_limit_id',
        'credit_amount',
        'total_amount',
        'sales_date',
        'credit_balance',
    ];

      protected static function boot()
{
    parent::boot();

    static::creating(function($salesinvoice) {
        // if (empty($createItem->batch_number)) {
        // dd($createItem);
            $salesinvoice->sales_invoice_number = static::generateSalesInvoiceNumber();
        // }
    });
}

private static function generateSalesInvoiceNumber()
{
        // dd('check');
    $prefix = 'HGV-SI';
    $timestamp = now()->format('YmdHis'); // Corrected method name
    $randomNumber = rand();

    return "{$prefix}-{$timestamp}-{$randomNumber}";
}

    public function salesorder()
    {
        return $this->belongsTo(SalesOrder::class);
    }
}
