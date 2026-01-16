<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\Syncable;

class LedgerPosting extends Model
{
    use Syncable, HasUuids;

    protected $primaryKey = 'posting_id';
    protected $keyType = 'string';
    public $incrementing = false;
    protected $table = 'ledger_postings';
    
    protected $fillable = [
        'posting_id',
        'line_id',
        'account_id',
        'posting_date',
        'credit_amount',
        'debit_amount',
        'sync_id',
        'location_id',
        'sync_status',
        'sync_version',
        'last_synced_at',
        'last_sync_attempt_at',
        'sync_error',
    ];
    
    protected $casts = [
        'posting_date' => 'date',
    ];

    public function uniqueIds()
    {
        return ['posting_id'];
    }
}