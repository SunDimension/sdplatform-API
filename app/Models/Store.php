<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    use HasFactory;

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

}
