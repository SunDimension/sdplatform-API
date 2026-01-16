<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Concerns\Syncable;
use Illuminate\Support\Str;

class SupplierInvoiceItem extends Model
{
    use HasFactory, HasUuid, Syncable;

    protected $primaryKey = 'invoice_item_id';
    public $incrementing = false;
    protected $keyType = 'string';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'invoice_id',
        'invoice_item_id',
        'product_id',
        'quantity',
        'unit_price',
        'amount',
        'sync_id',
        'location_id',
        'sync_status',
        'sync_version',
        'last_synced_at',
        'last_sync_attempt_at',
        'sync_error',
    ];


    protected $attributes = [
    'payment_id' => null,
    'payment_date' => null,
    'amount_paid' => null,
    'reference_no' => null,
];


    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'quantity' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'amount' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($supplierInvoiceItem) {
            // Ensure invoice_item_id is set if not already
            if (empty($supplierInvoiceItem->invoice_item_id)) {
                $supplierInvoiceItem->invoice_item_id = (string) Str::uuid();
            }
        });
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(PurchaseItemCost::class, 'product_id', 'id');
    }

    public function supplierInvoice(): BelongsTo
    {
        return $this->belongsTo(SupplierInvoice::class, 'invoice_id', 'invoice_id');
    }

}