<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\Syncable;

class JournalLine extends Model
{
    use Syncable, HasUuids;
    
    protected $table = 'journal_lines';
    protected $primaryKey = 'line_id';
    protected $keyType = 'string';
    public $incrementing = false;
    
    protected $fillable = [
        'line_id',
        'journal_id',
        'account_id',
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

    public function uniqueIds()
    {
        return ['line_id'];
    }
}