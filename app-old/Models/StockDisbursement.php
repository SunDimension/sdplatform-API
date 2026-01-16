<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Concerns\Syncable;
use Illuminate\Support\Str;
use Carbon\Carbon;

class StockDisbursement extends Model
{
    use HasFactory, HasUuid, Syncable;

    protected $primaryKey = 'disbursement_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'disbursement_date',
        'disbursement_type',
        'disbursement_number',
        'branch_id',
        'issued_by',
        // 'approved_by',S
        'remarks',
        'disbursement_id',
        'gr_id',
        // 'expiry_date',
           'sync_id',
        'location_id',
        'sync_status',
        'sync_version',
        'last_synced_at',
        'last_sync_attempt_at',
        'sync_error',
    ];

    protected $casts = [
        'disbursement_date' => 'date',
    ];

     protected static function boot()
    {
        parent::boot();

        static::creating(function ($stockDisbursement) {
            // Generate disbursement_number if it doesn't already exist
            if (empty($stockDisbursement->disbursement_number)) {
                $stockDisbursement->disbursement_number = 'DIS-' . strtoupper(Str::random(8));
            }

            // Ensure disbursement_id is set if not already
            if (empty($stockDisbursement->disbursement_id)) {
                $stockDisbursement->disbursement_id = (string) Str::uuid();
            }
        });
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function issuedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'issued_by');
    }

    public function approvedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function stockDisbursementItems(): HasMany
    {
        return $this->hasMany(
            StockDisbursementItem::class,
            'disbursement_id',
            'disbursement_id'
        );
    }
     public function goodsRecieved(): BelongsTo
     {
         return $this->belongsTo(GoodsRecieved::class, 'gr_id', 'gr_id');
     }
}
