<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\Concerns\Syncable;
class ItemSold extends Model
{
    use HasFactory, HasUuid, SoftDeletes, Syncable;

    public $table = "item_solds";

    protected $keyType = 'string';
    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'sales_order_id',
        'product_id',
        'quantity',
        'quantity_pieces',
        'unit_measurement',
        'unit_price',
        'amount',
        'store_id',
        'sales_date',
        'discount',
        'status',
        'return_quantity',
        'return_quantity_pieces',
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
        'sales_order_id' => 'string',
        'store_id' => 'string',
        'product_id' => 'string',
        'quantity' => 'integer',
        'quantity_pieces' => 'integer',
        'unit_price' => 'decimal:2',
        'amount' => 'decimal:2',
        'discount' => 'decimal:2',
        'unit_measurement' => 'integer',
        'return_quantity' => 'decimal:2',
        'return_quantity_pieces' => 'integer',
        'sales_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Relationships
     */
    public function salesOrder(): BelongsTo
    {
        return $this->belongsTo(SalesOrder::class, 'sales_order_id');
    }

    public function salesReceipt(): BelongsTo
    {
        return $this->belongsTo(SalesReceipt::class);
    }

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class, 'store_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(CreateItem::class, 'product_id');
    }

    public function measurement(): BelongsTo
    {
        return $this->belongsTo(Measurement::class, 'unit_measurement');
    }

    /**
     * Scopes for common queries
     */
    public function scopeByStore(Builder $query, int $storeId): Builder
    {
        return $query->where('store_id', $storeId);
    }

    public function scopeByProduct(Builder $query, int $productId): Builder
    {
        return $query->where('product_id', $productId);
    }

    public function scopeByDateRange(Builder $query, string $fromDate, string $toDate): Builder
    {
        return $query->whereBetween('sales_date', [
            Carbon::parse($fromDate)->startOfDay(),
            Carbon::parse($toDate)->endOfDay()
        ]);
    }

    public function scopeWithProduct(Builder $query): Builder
    {
        return $query->with(['product' => function ($query) {
            $query->select('id', 'name');
        }]);
    }

    public function scopeWithStore(Builder $query): Builder
    {
        return $query->with(['store' => function ($query) {
            $query->select('id', 'name');
        }]);
    }

    /**
     * Accessor for formatted sales date
     */
    public function getFormattedSalesDateAttribute(): string
    {
        return $this->sales_date ? $this->sales_date->format('Y-m-d') : '';
    }

    /**
     * Accessor for net amount (amount - discount)
     */
    public function getNetAmountAttribute(): float
    {
        return $this->amount - $this->discount;
    }

    /**
     * Static method to get sales report data
     */
    public static function getSalesReport(int $storeId, string $fromDate, string $toDate, ?int $productId = null): \Illuminate\Support\Collection
    {
        $query = self::query()
            ->join('create_items', 'item_solds.product_id', '=', 'create_items.id')
            ->byStore($storeId)
            ->byDateRange($fromDate, $toDate);

        if ($productId) {
            $query->byProduct($productId);
        }

        return $query->select([
            'item_solds.product_id',
            'create_items.name as product_name',
            DB::raw('SUM(item_solds.quantity) as total_quantity'),
            DB::raw('SUM(item_solds.amount) as total_amount'),
            DB::raw('COUNT(item_solds.id) as total_transactions')
        ])
            ->groupBy('item_solds.product_id', 'create_items.name')
            ->orderBy('total_quantity', 'desc')
            ->get();
    }

    /**
     * Static method to get store products
     */
    public static function getStoreProducts(int $storeId): \Illuminate\Support\Collection
    {
        return self::join('create_items', 'item_solds.product_id', '=', 'create_items.id')
            ->byStore($storeId)
            ->select('create_items.id', 'create_items.name')
            ->distinct()
            ->orderBy('create_items.name')
            ->get();
    }
}
