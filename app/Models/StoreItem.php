<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StoreItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_category_id',
        'create_item_id',
        'cost_price',
        'selling_price',
        'reorder_level',
        'quantity',
        'store_id',
        'branch_id',
        'discount',
        'quantity_holding',
        'set_limit'

    ];

    protected $cast = [
        'id' => 'integer',
        'item_category_id' => 'integer',
        'create_item_id' => 'integer',
        'branch_id' => 'integer',
        'store_id' => 'integer',
        'quantity_holding' => 'integer'
    ];

    public function createItem()
    {
        return $this->belongsTo(CreateItem::class,'create_item_id');
    }

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}
