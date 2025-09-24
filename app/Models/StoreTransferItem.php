<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Concerns\Syncable;
class StoreTransferItem extends Model
{
    use HasFactory, HasUuids, SoftDeletes, Syncable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'transfer_order_id',
        'quantity',
        'quantity_pieces',
        'unit_price',
        'product_id',
        'created_by',
        'modified_by',
        'deleted_by',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'unit_price' => 'double',
        'product_id' => 'string',
        'quantity' => 'integer',
        'quantity_pieces' => 'integer',
        'created_by' => 'string',
        'modified_by' => 'string',
        'deleted_by' => 'string',
    ];

    public function transferOrder(): BelongsTo
    {
        return $this->belongsTo(StoreTransferOrder::class, "transfer_order_id");
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(CreateItem::class, "product_id");
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class,"created_by");
    }

    public function modifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class,'modified_by');
    }

    public function deletedBy(): BelongsTo
    {
        return $this->belongsTo(User::class,'deleted_by');
    }
}
