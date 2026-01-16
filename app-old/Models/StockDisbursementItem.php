<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;
use App\Traits\HasUuid;

use App\Models\Concerns\Syncable;


class StockDisbursementItem extends Model
{
    use HasFactory, Syncable, HasUuid;

    protected $table = 'stock_disbursement_items';
    protected $primaryKey = 'disbursement_item_id';

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'disbursement_id',
        'product_id',
        'batch_number',
        'expiry_date',
        'quantity_issued',
        'unit_cost',
        'gr_item_id',
        'sync_id',
        'location_id',
        'sync_status',
        'sync_version',
        'last_synced_at',
        'last_sync_attempt_at',
        'sync_error',
    ];

    protected $casts = [
        'expiry_date' => 'date',
        'quantity_issued' => 'integer',
        'unit_cost' => 'decimal:2',
        'gr_item_id' => 'string',
    ];

    protected static function booted()
    {
        static::creating(function ($model) {
            if (!$model->disbursement_item_id) {
                $model->disbursement_item_id = (string) Str::uuid();
            }
        });
    }

    public function stockDisbursement(): BelongsTo
    {
        return $this->belongsTo(
            StockDisbursement::class,
            'disbursement_id',
            'disbursement_id'
        );
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(
            PurchaseItemCost::class,
            'product_id',
            'product_id'
        );
    }
}
