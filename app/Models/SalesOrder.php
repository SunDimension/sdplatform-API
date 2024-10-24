<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SalesOrder extends Model
{
    use HasFactory,SoftDeletes;


    public $table = "sales_orders";
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
        'user_id',
        'credit_limit',
        'credit_amount',
        'total_amount',
        'sales_date',
        'credit_balance',
        'payment_type',
        'status'
    ];
protected static function boot()
{
    parent::boot();

    // Automatically generate sales_order_number when a new SalesOrder is created
    static::creating(function ($salesOrder) {
        $randomNumber = str_pad(mt_rand(0, 9999999), 7, '0', STR_PAD_LEFT); // Generates a random 7-digit number
        $salesOrder->sales_order_number = 'HGV-SO-' . $randomNumber;
    });
}
      public function itemsold() :hasMany

    {
        return $this->hasMany(ItemSold::class, 'sales_order_id');
    }

   

  public function salesInvoices()
    {
        return $this->hasMany(SalesInvoice::class);
    }

    public function salesReceipts()
    {
        return $this->hasMany(SalesReceipt::class, "sales_order_id","id");
    }

    public function store()
    {
        return $this->belongsTo(Store::class, 'store_id');
    }
    public function branch()
    {
        return $this->belongsTo(Branch::class,'branch_id');
    }

      public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
