<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasUuid;
use App\Models\Concerns\Syncable;

class Store extends Model
{
    use HasFactory, Syncable;

    protected $fillable = [
        'store_type_id',
        'name',
        'branch_id',
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
        'id' => 'string',
        'store_type_id' => 'string',
        'branch_id' => 'string',
        'sync_id' => 'string',
        'location_id' => 'string',
        'sync_version' => 'integer',
        'last_synced_at' => 'datetime',
        'last_sync_attempt_at' => 'datetime',
    ];

    public function storetype(): BelongsTo
    {
        return $this->belongsTo(StoreType::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function storeItems()
    {
        return $this->hasMany(StoreItem::class);
    }

    public function itemSolds()
    {
        return $this->hasMany(ItemSold::class);
    }


/**
 * Get all product audits for this store
 */
public function productAudits()
{
    return $this->hasMany(ProductAudit::class);
}
}
