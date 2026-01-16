<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Concerns\Syncable;
class Release extends Model
{

    use HasFactory, SoftDeletes, HasUuid, Syncable;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [

        'sales_receipt_id',
        'branch_id',
        'store_id',
        'customer_id',
        'release_date',
        'user_id'
    ];

    protected $cast = [

        'id' => 'string',
        'branch_id' => 'string',
        'sales_receipt_id' => 'string',
        'store_id' => 'string',
        'customer_id' => 'string',
        'release_date' => 'date',
        'user_id' => 'string'


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

    // protected static function booted()
    // {
    //     static::created(fn ($model) => dispatch(new SyncModelJob($model)));
    //     static::updated(fn ($model) => dispatch(new SyncModelJob($model)));
    //     static::deleted(fn ($model) => dispatch(new SyncModelJob($model, 'delete')));
    // }
}
