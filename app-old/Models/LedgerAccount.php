<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class LedgerAccount extends Model
{
    use HasFactory, HasUuids;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $keyType = 'string';
    public $incrementing = false;

    protected $primaryKey = 'account_id';
    protected $table = 'ledger_accounts';
    protected $fillable = [
        'account_name',
        'account_code',
        'account_type_id',
        'created_at',
        'updated_at',
        'account_id',
    ];



    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'created_at' => 'integer',
        'account_type_id' => 'string',
        'account_id' => 'string',
    ];


    public function accountType(): BelongsTo
    {
        return $this->belongsTo(AccountType::class, 'account_type_id');
    }
}
