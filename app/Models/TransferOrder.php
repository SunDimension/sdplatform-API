<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransferOrder extends Model
{
    use HasFactory;

    public $table = "transfer_orders";

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'transfer_order_number',
        'transfer_date',
        'transfer_reason',
        'source_id',
        'destination_id',
        'image_url',
        'transfer_quantity',
        'item_id',
        'created_by',
        'updated_by'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'created_by' => 'integer',
        'updated_by' => 'integer',
        'transfer_date' => 'date',
    ];

     protected static function boot()
{
    parent::boot();

    static::creating(function($transferOrder) {
        // if (empty($createItem->batch_number)) {
        // dd($createItem);
            $transferOrder->transfer_order_number = static::generateTransferOrderNumber();
        // }
    });
}

private static function generateTransferOrderNumber()
{
        // dd('check');
    $prefix = 'HGV-TO';
    $timestamp = now()->format('YmdHis'); // Corrected method name
    $randomNumber = rand();

    return "{$prefix}-{$timestamp}-{$randomNumber}";
}

    public function sourceWarehouse(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    public function destinationWarehouse(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(CreateItem::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
