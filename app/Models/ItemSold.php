<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ItemSold extends Model
{
    use HasFactory, SoftDeletes;

    public $table ="item_solds";

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'sales_order_id',
        'product_id',
        'quantity',
        'unit_price',
        'amount',
        'store_id',
        'sales_date',
        
    ];

     /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'sales_order_id' => 'integer',
        'store_id'=>'integer,'
     
    ];

     public function salesOrder(): BelongsTo
    {
        return $this->belongsTo(SalesOrder::class);
    }

      public function salesReceipt()
    {
        return $this->belongsTo(SalesReceipt::class);
    }
    public function store()
    {
        return $this->belongsTo(Store::class);
    }
}
