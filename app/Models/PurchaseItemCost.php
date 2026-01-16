<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Concerns\Syncable;
use Illuminate\Support\Str;

class PurchaseItemCost extends Model
{
    use HasFactory, HasUuid, Syncable;

    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'product_id',
        'old_cost_price',
        'new_cost_price',
        'sync_id',
        'location_id',
        'sync_status',
        'sync_version',
        'last_synced_at',
        'last_sync_attempt_at',
        'sync_error',
        'id'
    ];

    protected $casts = [
        'id' => 'string',
        'product_id' => 'string',
        'old_cost_price' => 'decimal:2',
        'new_cost_price' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }
        });
    }

    /**
     * Get the actual product/item from create_items table
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(CreateItem::class, 'product_id', 'id');
    }

      
}