<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Models\Concerns\Syncable;
// use Illuminate\Support\Str;
use Carbon\Carbon;

class Supplier extends Model
{
    use HasFactory, Syncable;

    /**
     * Primary key settings (UUID)
     */
    protected $primaryKey = 'supplier_id';
    protected $keyType = 'string';
    public $incrementing = false;

    /**
     * Mass assignable fields
     */
    protected $fillable = [
        'supplier_id',
        'supplier_code',
        'supplier_name',
        'contact_person',
        'phone',
        'email',
        'address',
        'bank_id',
        'bank_acct_name',
        'bank_acct_num',
        'payment_terms',
        'status',
        'sync_id',
        'location_id',
        'sync_version',
        'sync_status',
        'last_sync_at',
        'last_sync_attempt',
        'sync_error',
    ];

    /**
     * Attribute casting
     */
    protected $casts = [
        'supplier_id' => 'string',
        'bank_id' => 'integer',
        'sync_version' => 'integer',
        'last_sync_at' => 'datetime',
        'last_sync_attempt' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Boot method
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($supplier) {
            // Generate UUID primary key
            if (empty($supplier->supplier_id)) {
                $supplier->supplier_id = (string) Str::uuid();
            }

            // Generate supplier code
            if (empty($supplier->supplier_code)) {
                $supplier->supplier_code = static::generateSupplierCode();
            }
        });
    }

    /**
     * Generate unique supplier code (SUP000001)
     */
    public static function generateSupplierCode(): string
    {
        $prefix = 'SUP';

        $lastSupplier = static::whereNotNull('supplier_code')
            ->orderBy('created_at', 'desc')
            ->first();

        if (!$lastSupplier) {
            return $prefix . '000001';
        }

        $lastNumber = (int) substr($lastSupplier->supplier_code, strlen($prefix));
        $nextNumber = $lastNumber + 1;

        return $prefix . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Relationships
     */
    public function bank()
    {
        return $this->belongsTo(Bank::class, 'bank_id');
    }

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeSynced($query)
    {
        return $query->where('sync_status', 'synced');
    }

    public function scopePendingSync($query)
    {
        return $query->where('sync_status', 'pending');
    }
}
