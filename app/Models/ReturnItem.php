<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReturnItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [

        'release_id',
        'sales_receipt_id',
        'customer_id',
        'branch_id',
        'return_date',
        'notes',
        'created_by',
        'approved_by',
        'approved_at',
        'return_status',
        'approval_comment'

    ];

    protected $cast = [
        'id' => 'integer',
        'release_id' => 'integer',
        'sales_receipt_id' => 'integer',
        'branch_id' => 'integer',
        // 'store_id'=>'integer',
        'created_by' => 'integer',
        'approved_by' => 'integer',

    ];


    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }
    public function stores(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }
   public function salesReceipt(): BelongsTo
{
    return $this->belongsTo(SalesReceipt::class, 'sales_receipt_id');
}

    public function release(): BelongsTo
    {
        return $this->belongsTo(Release::class);
    }
    
    public function returnDetails(): HasMany
    {
        return $this->hasMany(ReturnDetails::class, 'return_id');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
    
}
