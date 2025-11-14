<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Concerns\Syncable;

class CreditTransaction extends Model
{
    use HasFactory, SoftDeletes, Syncable, HasUuid;


    protected $keyType = 'string';
    public $incrementing = false;

    /**
     * The attributes that are mass assignable.

     
     *
     * @var array
     */
    protected $fillable = [
        'branch_id',
        'customer_id',
        'sales_order_id',
        'sales_receipt_id',
        'amount',
        'credit_limit',
        'credit_balance_before',
        'credit_balance_after',
        'type',
        'created_by',
        'modified_by',
        'deleted_by',
        'transaction_date',
        'notes',
        'sync_id',
        'location_id',
        'sync_status',
        'sync_version',
        'last_synced_at',
        'last_sync_attempt_at',
        'sync_error',
        'id'

    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'string',
        'branch_id' => 'string',
        'customer_id' => 'string',
        'sales_order_id' => 'string',
        'sales_receipt_id' => 'string',
        'created_by' => 'integer',
        'modified_by' => 'integer',
        'deleted_by' => 'integer',
    ];


    protected static function boot()
    {
        parent::boot();

        static::creating(function ($creditOrder) {
            do {
                $randomNumber = str_pad(mt_rand(0, 9999999), 7, '0', STR_PAD_LEFT);
                $creditOrderNumber = 'HGV-CR-' . $randomNumber;
            } while (static::where('credit_order_number', $creditOrderNumber)->exists());

            $creditOrder->credit_order_number = $creditOrderNumber;
        });
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, "customer_id");
    }

    public function salesOrder(): BelongsTo
    {
        return $this->belongsTo(SalesOrder::class);
    }

    public function salesReceipt(): BelongsTo
    {
        return $this->belongsTo(SalesReceipt::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function modifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function deletedBy(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
