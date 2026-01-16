<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChartOfAccount extends Model
{
    use HasFactory;

    protected $table = 'ledger_accounts'; // your actual table

    protected $fillable = [
        'account_code',
        'account_name',
        'account_type_id',
    ];

    public function accountType()
    {
        return $this->belongsTo(AccountType::class, 'account_type_id', 'account_type_id');
    }

}
