<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Models\Concerns\Syncable;

class SalesOrder extends Model
{
    use HasFactory, SoftDeletes, HasUuids, Syncable;

    public $table = "sales_orders";

    protected $keyType = 'string';
    public $incrementing = false;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'sales_order_number',
        'customer_id',
        'branch_id',
        'store_id',
        'user_id',
        'credit_limit',
        'credit_amount',
        'total_amount',
        'sales_date',
        'credit_balance',
        'payment_type',
        'status',
        'id',
        'sync_id',
        'location_id',
        'sync_status',
        'sync_version',
        'last_synced_at',
        'last_sync_attempt_at',
        'sync_error',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($salesOrder) {
            // CRITICAL FIX: Only generate sales_order_number if it doesn't already exist
            // This prevents regeneration during sync operations
            if (empty($salesOrder->sales_order_number)) {
                do {
                    $randomNumber = str_pad(mt_rand(0, 9999999), 7, '0', STR_PAD_LEFT);
                    $salesOrderNumber = 'HGV-SO-' . $randomNumber;
                } while (static::where('sales_order_number', $salesOrderNumber)->exists());

                $salesOrder->sales_order_number = $salesOrderNumber;
            }
            
            // Generate sync_id if not present (for new local records)
            if (empty($salesOrder->sync_id)) {
                $salesOrder->sync_id = (string) \Illuminate\Support\Str::uuid();
            }
            
            // Set location_id if not present
            if (empty($salesOrder->location_id)) {
                $salesOrder->location_id = config('app.location_id');
            }
            
            // Set sync_status if not present
            if (empty($salesOrder->sync_status)) {
                $salesOrder->sync_status = 'pending';
            }
            
            // Set sync_version if not present
            if (empty($salesOrder->sync_version)) {
                $salesOrder->sync_version = 1;
            }
        });
    }

    public function itemsold(): hasMany
    {
        return $this->hasMany(ItemSold::class, 'sales_order_id');
    }

    public function salesReceipts()
    {
        return $this->hasMany(SalesReceipt::class, "sales_order_id", "id");
    }

    public function store()
    {
        return $this->belongsTo(Store::class, 'store_id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, "customer_id");
    }

    public function measurement()
    {
        return $this->belongsTo(Measurement::class, "unit_measurement");
    }

    public function creditTransaction()
    {
        return $this->hasOne(CreditTransaction::class, 'sales_order_id')
            ->where('type', 'Credit');
    }

    public function productAudit(): HasMany
    {
        return $this->hasMany(ProductAudit::class, 'reference_id')
            ->where('reference_type', 'SalesOrder');
    }
}