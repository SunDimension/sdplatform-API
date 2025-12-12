<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JournalEntry extends Model
{
    protected $table = 'journal_entry'; // ✅ Make sure this is set
    
    protected $primaryKey = 'journal_id'; // ✅ Since you're using journal_id, not id
    
    public $incrementing = false; // ✅ Since journal_id is UUID
    
    protected $keyType = 'string'; // ✅ UUID is string
    
    protected $fillable = [
        'journal_id',
        'transaction_id',
        'entry_date',
        'description',
    ];
    
    protected $casts = [
        'entry_date' => 'date',
    ];
}