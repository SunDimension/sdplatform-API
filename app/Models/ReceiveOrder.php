<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReceiveOrder extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'purchase_order_number',
        'receive_date',
        'store_id',
        'branch_id',
        'vendor_id',
        'driver_name',
        'driver_phone',
        'truck_number',
        'quantity_pieces',
        'unit_measurement',
        'status',
        'created_by',
        'modified_by',
        'deleted_by',
        'approval_date',
        'approved_by',
        'approval_comment'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'receive_date' => 'date',
        'branch_id' => 'integer',
        'store_id' => 'integer',
        'created_by' => 'integer',
        'modified_by' => 'integer',
        'deleted_by' => 'integer',
        'approval_date'=> 'date',
        'approved_by'=> 'integer'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($receiveOrder) {
            do {
                $randomNumber = str_pad(mt_rand(0, 9999999), 7, '0', STR_PAD_LEFT);
                $number = 'HGV-RO-'. $randomNumber;
            } while (static::where('purchase_order_number', $number)->exists());

            // $receiveOrder->purchase_order_number = $number;
            $receiveOrder->created_by = auth()->user()->id;
            $receiveOrder->status = 'Pending';
            
        });

    }
    public function receiveItems(): HasMany
    {
        return $this->hasMany(ReceiveItem::class);
    }

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, "created_by");
    }

    public function modifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'modified_by');
    }

    public function deletedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

       public function measurement()
    {
        return $this->belongsTo(Measurement::class,"unit_measurement");
    }
}
