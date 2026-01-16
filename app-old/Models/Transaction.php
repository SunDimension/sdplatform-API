<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\Syncable;
use Illuminate\Support\Str;

class Transaction extends Model
{
    use Syncable, HasUuids;

    protected $primaryKey = 'transaction_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'transaction_id',
        'transaction_type',
        'reference_id',
        'description',
        'transaction_date',
        'total_amount',
        'sync_id',
        'location_id',
        'sync_version',
        'sync_status',
        'last_synced_at',        // ✅ Fixed
        'last_sync_attempt_at',  // ✅ Fixed
        'sync_error',
    ];

    protected $casts = [
        'transaction_date' => 'date', // Changed from 'datetime' to match your usage
        'total_amount' => 'decimal:2',
    ];

    // Override the UUID column name
    public function uniqueIds()
    {
        return ['transaction_id'];
    }

    public function transactionType()
    {
        return $this->belongsTo(TransactionType::class, 'transaction_type', 'type_code');
    }
}