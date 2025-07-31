<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
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
        'user_id'
    ];

    protected $cast = [

        'id' => 'integer',
        'branch_id' => 'integer',
        'sales_receipt_id' => 'integer',
        'store_id' => 'integer',
        'customer_id' => 'integer',
        'release_date' => 'date',
        'user_id' => 'integer'


    ];


    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }
    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }


public function salesReceipt()
{
    return $this->belongsTo(SalesReceipt::class, 'sales_receipt_id');
}

    public function releasedetail(): HasMany
    {
        return $this->hasMany(ReleaseDetails::class);
    }
    //  public function createItem()
    // {
    //     return $this->belongsTo(CreateItem::class); // Each release belongs to a CreateItem
    // }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
