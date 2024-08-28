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

    static::creating(function($salesorder) {
        // if (empty($createItem->batch_number)) {
        // dd($createItem);
            $salesorder->sales_order_number = static::generateSalesOrderNumber();
        // }
    });
}

private static function generateSalesOrderNumber()
{
        // dd('check');
    $prefix = 'HGV-SO';
    $timestamp = now()->format('YmdHis'); // Corrected method name
    $randomNumber = rand();

    return "{$prefix}-{$timestamp}-{$randomNumber}";
}


    public function itemsold() :hasMany

    {
        return $this->hasMany(ItemSold::class);
    }

    public function branch() : BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

     public function store() : BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

     public function creditLimit() : BelongsTo
    {
        return $this->belongsTo(CreditLimit::class);
    }

    
     public function customer() : BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }
}
