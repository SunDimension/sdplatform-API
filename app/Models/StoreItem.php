<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StoreItem extends Model
{
    use HasFactory, HasUuid;

    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    /**
     * The table associated with the model.
     */
    protected $table = 'store_items';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'item_category_id',
        'create_item_id',
        'unit_id',
        'quantity',
        'quantity_holding',
        'cost_price',
        'selling_price',
        'reorder_level',
        'discount',
        'user_id',
        'branch_id',
        'discount',
        'quantity_holding',
        'set_limit',
        'quantity_in_package', // Add this line
        'selling_price_per_unit', 

    ];

    protected $cast = [
        'id' => 'string',
        'item_category_id' => 'integer',
        'create_item_id' => 'string',
        'branch_id' => 'string',
        'store_id' => 'string',
        'quantity_holding' => 'integer',
        'set_limit' => 'integer', // or 'float' if it can be a decimal value
    ];

    public function createItem()
    {
        return $this->belongsTo(CreateItem::class,'create_item_id');
    }

    public function store()
    {
        return $this->belongsTo(Store::class, 'store_id');
    }

    public static function getCurrentQuantity($productId, $storeId)
{
    $item = self::where('create_item_id', $productId)
        ->where('store_id', $storeId)
        ->first();
        
    return $item ? $item->quantity : 0;
}

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}
