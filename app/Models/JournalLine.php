<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JournalLine extends Model
{
    protected $table = 'journal_lines'; 
    
    protected $fillable = [
        'account_id',
        'credit_amount',
        'debit_amount',
        'created_at',
    ];
    

    public function account()
    {
        return $this->belongsTo(LedgerAccount::class, 'account_id');
    }
}