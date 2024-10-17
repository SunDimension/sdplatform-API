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
        'unit_id',
        'cost_price',
        'selling_price',
        'reorder_level',
        'quantity',
        'store_id',
        'user_id',
        'discount',

    ];


    protected $cast = [
        'id'=>'integer',
        'item_category_id'=>'integer',
        'create_item_id'=>'integer',
        'unit_id'=>'integer',
        'store_id'=>'integer',
        'user_id'=>'integer'
    ];
}
