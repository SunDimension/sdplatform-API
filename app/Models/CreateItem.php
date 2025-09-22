<?php

namespace App\Models;

use App\Models\Concerns\Syncable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

// use Illuminate\Database\Eloquent\SoftDeletes;


class CreateItem extends Model
{
    use HasFactory, Syncable, HasUuid;


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
        'user_id',
        'tax_id',
        'is_tax_inclusive',
        'tax_amount'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'string',
        'item_category_id' => 'integer',
        'item_type_id' => 'integer',
        'unit_id' => 'integer',
        'brand_id' => 'integer',
        'batch_number'=>'string',
        'cost_price' => 'float',
        'selling_price' => 'float',
        'dimension_id' => 'integer',
        'weight_id' => 'integer',
        'branch_id' => 'string',
        'warehouse' => 'integer',
        'vendor_id' => 'string',
        // 'store_id' => 'string',
        'user_id' => 'string',
        'tax_id' => 'integer',
        'is_tax_inclusive' => 'boolean',
        'tax_amount' => 'float'
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

    static::saving(function($createItem) {
        if ($createItem->isDirty('selling_price') || $createItem->isDirty('tax_id') || $createItem->isDirty('is_tax_inclusive')) {
            $createItem->calculateTaxAmount();
        }
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

public function calculateTaxAmount()
{
    if (!$this->tax_id) {
        $this->tax_amount = 0;
        return;
    }

    $tax = Tax::find($this->tax_id);
    if (!$tax) {
        $this->tax_amount = 0;
        return;
    }

    if ($this->is_tax_inclusive) {
        // If price is tax inclusive, calculate tax amount from the total price
        $this->tax_amount = $this->selling_price - ($this->selling_price / (1 + ($tax->rate / 100)));
    } else {
        // If price is tax exclusive, calculate tax amount from the base price
        $this->tax_amount = $this->selling_price * ($tax->rate / 100);
    }
}

public function getTaxInclusivePrice()
{
    if ($this->is_tax_inclusive) {
        return $this->selling_price;
    }
    return $this->selling_price + $this->tax_amount;
}

public function getTaxExclusivePrice()
{
    if (!$this->is_tax_inclusive) {
        return $this->selling_price;
    }
    return $this->selling_price - $this->tax_amount;
}

public function tax(): BelongsTo
{
    return $this->belongsTo(Tax::class);
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


    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    public function storeItems(): HasMany
    {
        return $this->hasMany(StoreItem::class);
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
