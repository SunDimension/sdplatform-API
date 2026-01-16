<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountType extends Model
{
    use HasFactory;

    protected $table = 'account_type'; 
    protected $primaryKey = 'account_type_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'account_type',
    ];

    public function chartOfAccounts()
    {
        return $this->hasMany(ChartOfAccount::class, 'account_type_id', 'account_type_id');
    }
}
