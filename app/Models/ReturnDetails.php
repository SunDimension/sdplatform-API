<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReturnDetails extends Model
{
    use HasFactory,SoftDeletes ;


    protected $fillable = [

        'return_id',
        'product_id',
        'return_quantity',
    ];

    protected $cast = [

        'id'=>'integer',
        'return_id'=>'integer',
        'product_id'=>'integer',

    ];

    public function product():BelongsTo
    {
        return $this->belongTo(CreateItem::class);
    }

     public function returnitem():BelongsTo
    {
        return $this->belongTo(ReturnItem::class);
    }


}
