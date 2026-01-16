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

class SupplierInvoice extends Model
{
    use HasFactory, HasUuid, Syncable;

    protected $primaryKey = 'invoice_id';
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
        // 'payment_id',
        'status',
        'created_by',
        'sync_id',
        'location_id',
        'sync_status',
        'sync_version',
        'last_synced_at',
        'last_sync_attempt_at',
        'sync_error',
        'invoice_id',
        'invoice_number',
        'invoice_date',
        'due_date',
        'total_amount',
        'gr_id',
        'approved_by',
        'approved_at',
    ];

protected static function booted()
{
    static::saving(function ($model) {
        foreach ([
            'payment_id',
            'payment_date',
            'amount_paid',
            'reference_no',
        ] as $field) {
            if ($model->$field === '') {
                $model->$field = null;
            }
        }
    });
    

}
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'invoice_date' => 'datetime',
        'due_date' => 'datetime',
        'approved_at' => 'datetime',
        'payment_date' => 'datetime',
        'total_amount' => 'decimal:2',
        'amount_paid' => 'decimal:2',
        'status' => 'string', // Add this cast
    ];


protected $attributes = [
    'amount_paid' => null,
    'payment_id' => null,
    'payment_date' => null,
    'reference_no' => null,
];


public function setAmountPaidAttribute($value)
{
    $this->attributes['amount_paid'] =
        ($value === '' || $value === null) ? null : $value;
}
 

    // public function setStatusAttribute($value)
    // {
    //     $this->attributes['STATUS'] = $value; // Set uppercase column
    // }

    // public function getStatusAttribute()
    // {
    //     return $this->attributes['STATUS'] ?? null; // Get uppercase column
    // }
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($supplierInvoice) {
            // Generate invoice_number if it doesn't already exist
            if (empty($supplierInvoice->invoice_number)) {
                $supplierInvoice->invoice_number = 'INV-' . strtoupper(Str::random(8));
            }

            // Ensure invoice_id is set if not already
            if (empty($supplierInvoice->invoice_id)) {
                $supplierInvoice->invoice_id = (string) Str::uuid();
            }
        });
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    public function goodsReceived(): BelongsTo
    {
        return $this->belongsTo(GoodsRecieved::class, 'gr_id', 'gr_id');
    }

    public function supplierInvoiceItems(): HasMany
    {
        return $this->hasMany(SupplierInvoiceItem::class, 'invoice_id', 'invoice_id');
    }

    public function createdByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function approvedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by', 'id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(SupplierPayment::class, 'invoice_id', 'invoice_id');
    }

    /**
     * Check if the invoice is overdue
     *
     * @return bool
     */
    public function isOverdue(): bool
    {
        if (!$this->due_date || $this->status === 'paid') {
            return false;
        }

        return Carbon::parse($this->due_date)->isPast();
    }

    /**
     * Get the overdue days count
     *
     * @return int|null
     */
    public function getOverdueDaysAttribute(): ?int
    {
        if (!$this->due_date || $this->status === 'paid' || !$this->isOverdue()) {
            return null;
        }

        return Carbon::parse($this->due_date)->diffInDays(Carbon::now());
    }

    /**
     * Get the balance due amount
     *
     * @return float
     */
    public function getBalanceDueAttribute(): float
    {
        return $this->total_amount - ($this->amount_paid ?? 0);
    }

    /**
     * Check if invoice is fully paid
     *
     * @return bool
     */
    public function isFullyPaid(): bool
    {
        return $this->balance_due <= 0;
    }

    /**
     * Check if invoice is partially paid
     *
     * @return bool
     */
    public function isPartiallyPaid(): bool
    {
        return ($this->amount_paid ?? 0) > 0 && $this->balance_due > 0;
    }

    /**
     * Scope a query to only include overdue invoices
     */
    public function scopeOverdue($query)
    {
        return $query->where('due_date', '<', Carbon::now())
            ->where('status', '!=', 'paid');
    }

    /**
     * Scope a query to only include unpaid invoices
     */
    public function scopeUnpaid($query)
    {
        return $query->where('status', 'unpaid');
    }

    /**
     * Scope a query to only include partially paid invoices
     */
    public function scopePartiallyPaid($query)
    {
        return $query->where('status', 'partial');
    }

    /**
     * Scope a query to only include paid invoices
     */
    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }


}
