<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\HasUuid;
use App\Models\Concerns\Syncable;

class ReturnDetails extends Model
{
    use HasFactory, SoftDeletes, HasUuid, Syncable;

    protected $fillable = [
        'return_id',
        'product_id',
        'return_quantity',
        'return_quantity_pieces',
        'item_sold_id',
        'unit_price',
        'discount',
        'store_id',
        'unit_measurement',
        'id',
        'sync_id',
        'location_id',
        'sync_status',
        'sync_version',
        'last_synced_at',
        'last_sync_attempt_at',
        'sync_error',
    ];

    protected $casts = [ // Changed from 'cast' to 'casts'
        'id' => 'string',
        'return_id' => 'string',
        'product_id' => 'string',
        'item_sold_id' => 'string',
        'store_id' => 'string',
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

    public function measurement()
    {
        return $this->belongsTo(Measurement::class, 'unit_measurement', 'id'); // Fixed typo here
    }
}
