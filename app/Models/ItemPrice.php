<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ItemPrice extends Model
{
    use HasFactory, SoftDeletes;

    public $table ="item_prices";

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'product_id',
        'store_item',
        'selling_price',
        'user_id',
        'change_date',
        
        
    ];

     /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'store_item_id' => 'integer',
        'user_id'=>'integer'
     
    ];

    //  public function salesOrder(): BelongsTo
    // {
    //     return $this->belongsTo(SalesOrder::class, 'sales_order_id');
    // }

    //   public function salesReceipt()
    // {
    //     return $this->belongsTo(SalesReceipt::class);
    // }
    public function storeItem()
    {
        return $this->belongsTo(StoreItem::class);
    }

    public function product()
    {
        return $this->belongsTo(CreateItem::class,"product_id");
    }

      public function user()
    {
        return $this->belongsTo(User::class,"user_id");
    }
}
