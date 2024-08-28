<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Release extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [

        'sales_receipt_id',
        'branch_id',
        'store_id',
        'customer_id',
        'release_date',
];

    protected $cast = [

        'id' =>'integer',
        'branch_id'=>'integer',
        'sales_receipt_id'=>'integer',
        'store_id'=>'integer',
        'customer_id'=>'integer',
        'release_date'=>'integer'

        ];


 public function customer(): BelongsTo
        {
        return $this->belongsTo(Customer::class);
             }
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
}