<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\Syncable;

class Store extends Model
{
    use HasFactory, Syncable;

    protected $fillable = [
        'store_type_id',
        'name',
        'branch_id',

    ];


    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'store_type_id' => 'integer',
        'branch_id' => 'integer',

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
