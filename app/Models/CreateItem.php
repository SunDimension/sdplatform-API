<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
// use Illuminate\Database\Eloquent\SoftDeletes;


class CreateItem extends Model
{
    use HasFactory;


    //   public function getRouteKeyName()
    // {
    //     return 'name';
    // }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'item_category_id',
        'item_type_id',
        'description',
        'batch_number',
        'unit_id',
        'quantity',
        'brand_id',
        'cost_price',
        'selling_price',
        'reorder_level',
        'discount',
        'dimension_id',
        'weight_id',
        // 'branch_id',
        'warehouse',
        'vendor_id',
        'image_url',
        'barcode',
        'store_id',
        'user_id'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'item_category_id' => 'integer',
        'item_type_id' => 'integer',
        'unit_id' => 'integer',
        'brand_id' => 'integer',
        'batch_number'=>'string',
        'cost_price' => 'float',
        'selling_price' => 'float',
        'dimension_id' => 'integer',
        'weight_id' => 'integer',
        'branch_id' => 'integer',
        'warehouse' => 'integer',
        'vendor_id' => 'integer',
        'store_id' => 'integer',
        'user_id' => 'integer',
    ];

   protected static function boot()
{
    parent::boot();

    static::creating(function($createItem) {
        // if (empty($createItem->batch_number)) {
        // dd($createItem);
            $createItem->batch_number = static::generateBatchNumber();
        // }
    });
}

private static function generateBatchNumber()
{
        // dd('check');
    $prefix = 'HGV';
    $timestamp = now()->format('YmdHis'); // Corrected method name
    $randomNumber = rand();

    return "{$prefix}-{$timestamp}-{$randomNumber}";
}


    public function itemCategory(): BelongsTo
    {
        return $this->belongsTo(ItemCategory::class);
    }

    public function itemType(): BelongsTo
    {
        return $this->belongsTo(ItemType::class);
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

       public function discount(): BelongsTo
    {
        return $this->belongsTo(Discount::class);
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function dimension(): BelongsTo
    {
        return $this->belongsTo(Dimension::class);
    }

    public function weight(): BelongsTo
    {
        return $this->belongsTo(Weight::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class,'branch_id');
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }
       public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

      public function releases()
    {
        return $this->hasMany(Release::class); // A CreateItem can have many releases
    }

    
    // public function item($id)
// {
//     // Find the item by ID
//     $createItem = CreateItem::find($id);

//     if ($createItem) {
//         // Perform soft delete
//         $createItem->delete();
//     } else {
//         // Handle the case where the item was not found
//         return response()->json(['message' => 'Item not found'], 404);
//     }

//     // Retrieve all items, including those that are soft-deleted
//     $allItems = CreateItem::withTrashed()->get();

//     // Retrieve only soft-deleted items
//     $trashedItems = CreateItem::onlyTrashed()->get();

//     // Return the result (You can structure the response as needed)
//     return response()->json([
//         'all_items' => $allItems,
//         'trashed_items' => $trashedItems,
//     ]);
// }

   

}
