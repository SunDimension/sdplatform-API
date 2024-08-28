<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReleaseDetails extends Model
{
    use HasFactory,SoftDeletes ;

    protected $fillable = [

        'release_id',
        'product_id',
        'release_quantity',
        'amount'
    ];

    protected $cast = [

        'id'=>'integer',
        'release_id'=>'integer',
        'product_id'=>'integer',

    ];

     public function product():BelongsTo
    {
        return $this->belongTo(CreateItem::class);
    }

     public function release():BelongsTo
    {
        return $this->belongTo(Release::class);
    }
}
