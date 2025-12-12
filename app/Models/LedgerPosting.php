<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LedgerPosting extends Model
{
    protected $table = 'ledger_postings'; 
    
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