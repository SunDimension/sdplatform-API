<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReturnItem extends Model
{
    use HasFactory,SoftDeletes;

    protected $fillable = [
    
        'release_id',
        'sales_receipt_id',
        'branch_id',
        'store_id',
        'return_date'

    ];

   protected $cast = [
        'id'=>'integer',
        'release_id'=>'integer',
        'sales_receipt_id'=>'integer',
        'branch_id'=>'integer',
        'store_id'=>'integer',

   ];


  public function branch(): BelongsTo
        {
        return $this->belongsTo(Branch::class);
         }
 public function stores(): BelongsTo
    {
        return $this->belongsTo(Store::class);

    }
 public function salesreceipt(): BelongsTo
    {
        return $this->belongsTo(SalesReceipt::class);   
}

    public function release(): BelongsTo
    {
        return $this->belongsTo(Release::class);   
}
}
