<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReleaseDetails extends Model
{
    use HasFactory,SoftDeletes;

    protected $table ="release_details";

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
        return $this->belongsTo(CreateItem::class, "product_id");
    }

     public function release()
    {
        return $this->belongsTo(Release::class);
    }
}
