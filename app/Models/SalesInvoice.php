<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

use Illuminate\Database\Eloquent\Relations\HasMany;

class SalesInvoice extends Model
{
    use HasFactory;


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'sales_order_id',
        'sales_invoice_number',
        'amount',
        'unit_price',
        'invoice_date',
        'product_id'

    ];

   protected static function boot()
    {
        parent::boot();

        // Automatically generate sales_invoice_number when a new SalesInvoice is created
        static::creating(function ($salesInvoice) {
            $salesInvoice->sales_invoice_number = 'HGV-INV-' . strtoupper(uniqid());
        });
    }

    public function salesorder() : BelongsTo
    {
        return $this->belongsTo(SalesOrder::class);
    }
}
