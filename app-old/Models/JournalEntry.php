<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\Syncable;

class JournalEntry extends Model
{
    use Syncable, HasUuids;
    
    protected $table = 'journal_entry';
    protected $primaryKey = 'journal_id';
    public $incrementing = false;
    protected $keyType = 'string';
    
    protected $fillable = [
        'journal_id',
        'transaction_id',
        'entry_date',
        'description',
        'sync_id',
        'location_id',
        'sync_status',
        'sync_version',
        'last_synced_at',
        'last_sync_attempt_at',
        'sync_error',
    ];
    
    protected $casts = [
        'entry_date' => 'date',
    ];

    public function uniqueIds()
    {
        return ['journal_id'];
    }
}