<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReturnDetails extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'return_id',
        'product_id',
        'return_quantity',
        'return_quantity_pieces',
        'item_sold_id',
        'unit_price',
        'store_id',
        'unit_measurement',      
    ];

    protected $casts = [ // Changed from 'cast' to 'casts'
        'id' => 'integer',
        'return_id' => 'integer',
        'product_id' => 'integer',
        'item_sold_id' => 'integer',
        'store_id' => 'integer',
        'unit_measurement' => 'integer',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(CreateItem::class); // Fixed typo here
    }

    public function returnItem(): BelongsTo // Changed method name to camelCase
    {
        return $this->belongsTo(ReturnItem::class); // Fixed typo here
    }

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }
}