<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Concerns\Syncable;
class StoreTransferOrder extends Model
{
    use HasFactory, HasUuids, SoftDeletes, Syncable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'order_number',
        'transfer_date',
        'source_store_id',
        'destination_store_id',
        'status',
        'approved_by',
        'approved_at',
        'created_by',
        'modified_by',
        'deleted_by',
           'id',
        'sync_id',
        'location_id',
        'sync_status',
        'sync_version',
        'last_synced_at',
        'last_sync_attempt_at',
        'sync_error',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'transfer_date' => 'datetime',
        'source_store_id' => 'string',
        'destination_store_id' => 'string',
        'approved_at' => 'timestamp',
        'created_by' => 'string',
        'modified_by' => 'string',
        'deleted_by' => 'string',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($order) {
            do {
                $randomNumber = str_pad(mt_rand(0, 9999999), 7, '0', STR_PAD_LEFT);
                $OrderNumber = 'HGV-ST-' . $randomNumber;
            } while (static::where('order_number', $OrderNumber)->exists());
            $order->order_number = $OrderNumber;
        });
    }

    public function items(): HasMany
    {
        return $this->hasMany(StoreTransferItem::class, 'transfer_order_id');
    }

    public function sourceStore(): BelongsTo
    {
        return $this->belongsTo(Store::class, 'source_store_id');
    }

    public function destinationStore(): BelongsTo
    {
        return $this->belongsTo(Store::class, 'destination_store_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, "created_by");
    }

    public function modifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'modified_by');
    }

    public function deletedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }
}
