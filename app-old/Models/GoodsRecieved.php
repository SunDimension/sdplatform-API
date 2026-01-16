<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Concerns\Syncable;
use Illuminate\Support\Str;

class GoodsRecieved extends Model
{
    use HasFactory, HasUuid, Syncable;

    protected $primaryKey = 'gr_id';
    public $incrementing = false;
    protected $keyType = 'string';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'gr_number',
        'po_id',
        'recieved_date',
        'status',
        'received_by',
        'invoice_status',
        'remarks',
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
        'po_id' => 'string',
        'gr_id' => 'string',
        'recieved_date' => 'datetime',
        'status' => 'string',
        'recieved_by' => 'string',
        'remarks' => 'string',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($goodRecieved) {
            // REMOVE THIS BLOCK - You don't have an 'id' column
            // if (empty($purchaseOrder->id)) {
            //     $purchaseOrder->id = (string) Str::uuid();
            // }

            // CRITICAL FIX: Only generate po_number if it doesn't already exist
            if (empty($goodRecieved->gr_number)) {
                do {
                    $randomNumber = str_pad(mt_rand(0, 9999999), 7, '0', STR_PAD_LEFT);
                    $grNumber = 'HGV-GR-' . $randomNumber;
                } while (static::where('gr_number', $grNumber)->exists());
                $goodRecieved->gr_number = $grNumber;
            }

            // Ensure po_id is set if not already
            if (empty($goodRecieved->gr_id)) {
                $goodRecieved->gr_id = (string) Str::uuid();
            }
        });
    }
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class, 'supplier_id',);
    }

    public function purchaseOrderItems(): HasMany
    {
        return $this->hasMany(PurchaseOrderItem::class, 'po_id');
    }


    public function recievedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'received_by', 'id');
    }

    public function approvedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by', 'id');
    }

    public function purchaseOrder(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrder::class, 'po_id', 'po_id');
    }

    public function items()
    {
        return $this->hasMany(GoodsRecievedItem::class, 'gr_id', 'gr_id');
    }

    // In your GoodsRecieved model (App\Models\GoodsRecieved.php)
    public function goodsReceivedItems()
    {
        return $this->hasMany(GoodsRecievedItem::class, 'gr_id', 'gr_id');
    }

    // // This creates an alias so both names work
    // public function items()
    // {
    //     return $this->goodsReceivedItems();

    // 在 App\Models\GoodsRecieved 类中添加这个方法
    public function availableItems()
    {
        return $this->hasMany(GoodsRecievedItem::class, 'gr_id', 'gr_id')
            ->where(function ($query) {
                $query->where('is_depleted', false)
                    ->orWhereNull('is_depleted');
            })
            ->whereRaw('(quantity_received - quantity_damaged) > 0')
            ->with(['product', 'purchaseOrderItem', 'product.product']);
    }
}
// }
