<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AccountGroup extends Model
{
    use HasFactory, HasUuids;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'created_by',
        'modified_by',
        'deleted_by',
    ];

    public function accounts(): HasMany
    {
        return $this->hasMany(Account::class);
    }

    public function accountTypes(): HasMany
    {
        return $this->hasMany(AccountType::class);
    }
}
