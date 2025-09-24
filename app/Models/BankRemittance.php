<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasUuid;
use App\Models\Concerns\Syncable;
class BankRemittance extends Model
{
    use HasFactory, HasUuid, Syncable;

    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        // 'expense_name',
        'branch_id',
        'bank_id',
        'amount',
        'user_id',
        'store_id',
        'date',
        'account_number',
        'approval_comment',
        'status',
        
        

    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'string',
        'branch_id' => 'string',
        'user_id' => 'string',
        'bank_id' => 'string',
        'store_id' => 'string',
        
];
          public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }
       public function store()
    {
        return $this->belongsTo(Store::class);
    }
       public function user()
    {
        return $this->belongsTo(User::class, 'user_id');

    }

         public function bank()
    {
        return $this->belongsTo(Bank::class,);
    }

   
}
