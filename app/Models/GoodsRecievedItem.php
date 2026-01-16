<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Concerns\Syncable;
use Illuminate\Support\Str;

class GoodsRecievedItem extends Model
{
    use HasFactory, HasUuid, Syncable;

    protected $primaryKey = 'gr_item_id';
    public $incrementing = false;
    protected $keyType = 'string';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'gr_id',
        'po_item_id',
        // 'received_date',
        'product_id',
        'is_depleted',
        'quantity_received',
        'quantity_damaged',
        'expiry_date',
        'batch_number',
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
        'po_item_id' => 'string',
        'gr_item_id' => 'string',
        'received_date' => 'datetime',
        'product_id' => 'string',
        'quantity_received' => 'decimal:2',
        'quantity_damaged' => 'decimal:2',
        'expiry_date' => 'datetime',
        'is_depleted'=>'boolean'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($goodRecievedItem) {
            // REMOVE THIS BLOCK - You don't have an 'id' column
            // if (empty($purchaseOrder->id)) {
            //     $purchaseOrder->id = (string) Str::uuid();
            // }

            // CRITICAL FIX: Only generate po_number if it doesn't already exist
            // if (empty($goodRecieved->gr_number)) {
            //     do {
            //         $randomNumber = str_pad(mt_rand(0, 9999999), 7, '0', STR_PAD_LEFT);
            //         $grNumber = 'HGV-GR-' . $randomNumber;
            //     } while (static::where('gr_number', $grNumber)->exists());
            //     $goodRecieved->gr_number = $grNumber;
            // }

            // Ensure po_id is set if not already
            if (empty($goodRecievedItem->gr_item_id)) {
                $goodRecievedItem->gr_item_id = (string) Str::uuid();
            }
        });
    }
    // public function supplier(): BelongsTo
    // {
    //     return $this->belongsTo(Supplier::class, 'supplier_id',);
    // }

    // public function purchaseOrderItems(): HasMany
    // {
    //     return $this->hasMany(PurchaseOrderItem::class, 'po_id');
    // }


    public function recievedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'received_by', 'id');
    }

    public function approvedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by', 'id');
    }

    public function purchaseOrderItem(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrderItem::class, 'po_item_id', 'po_item_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(PurchaseItemCost::class, 'product_id', 'id');
    }

    public function goodRecieved(): BelongsTo
    {
        return $this->belongsTo(GoodsRecieved::class, 'gr_id', 'gr_id');
    }

    // In your GoodsRecieved model (App\Models\GoodsRecieved.php)
    // public function goodsReceivedItems()
    // {
    //     return $this->hasMany(GoodsRecievedItem::class, 'gr_id', 'gr_id');
    // }

    // // This creates an alias so both names work
    // public function items()
    // {
    //     return $this->goodsReceivedItems();
    // }

  
}
