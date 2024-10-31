<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReceiveItem extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'receive_order_id',
        'quantity',
        'unit_price',
        'product_id',
        'description',
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
        'product_id' => 'integer',
        'quantity' => 'integer',
        'created_by' => 'integer',
        'modified_by' => 'integer',
        'deleted_by' => 'integer',
    ];

    public function receiveOrder(): BelongsTo
    {
        return $this->belongsTo(ReceiveOrder::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(CreateItem::class,'product_id');
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
