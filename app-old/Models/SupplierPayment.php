<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Concerns\Syncable;
use Illuminate\Support\Str;

class SupplierPayment extends Model
{
    use HasFactory, HasUuid, Syncable;

    protected $primaryKey = 'payment_id';
    public $incrementing = false;
    protected $keyType = 'string';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [

        'payment_id',
        'amount_paid',
        'payment_date',
        'reference_no',
        'supplier_id',
        'status',
        'created_by',
        'sync_id',
        'location_id',
        'sync_status',
        'sync_version',
        'last_synced_at',
        'last_sync_attempt_at',
        'sync_error',
        'invoice_id'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    // protected $casts = [
    //     'invoice_date' => 'datetime',
    //     'due_date' => 'datetime',
    //     'approved_at' => 'datetime',
    //     'total_amount' => 'decimal:2',
    // ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($supplierPayment) {
            // Generate invoice_number if it doesn't already exist

            // Ensure invoice_id is set if not already
            if (empty($supplierPayment->invoice_id)) {
                $supplierPayment->invoice_id = (string) Str::uuid();
            }
        });
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }



    public function createdByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    // public function approvedByUser(): BelongsTo
    // {
    //     return $this->belongsTo(User::class, 'approved_by', 'id');
    // }
    public function invoice(): BelongsTo
    {
        return $this->belongsTo(SupplierInvoice::class, 'invoice_id', 'invoice_id');
    }
}
