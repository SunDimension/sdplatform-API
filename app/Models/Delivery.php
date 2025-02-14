<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Delivery extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'customer_id',
        'sales_order_number',
        'delivery_order_number',
        'delivery_date',
        'carrier_id',
        'notes',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'customer_id' => 'integer',
        'delivery_date' => 'timestamp',
        'carrier_id' => 'integer',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function carrier(): BelongsTo
    {
        return $this->belongsTo(Carrier::class);
    }
}
