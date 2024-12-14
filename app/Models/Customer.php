<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'customer_type_id',
        'title_id',
        'branch_id',
        'surname',
        'firstname',
        'middlename',
        'phone_number',
        'name',
        'email',
        'address',
        'credit_limit',
        'credit_balance'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'customer_type_id' => 'integer',
        'title_id' => 'integer',
        'credit_limit' => 'decimal:2',
        'credit_balance' => 'decimal:2'
    ];

    public function customerType(): BelongsTo
    {
        return $this->belongsTo(CustomerType::class);
    }

    public function title(): BelongsTo
    {
        return $this->belongsTo(Title::class);
    }
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

 

    public function salesOrder(): HasMany
    {
        return $this->hasMany(SalesOrder::class);
    }
    public function inflows()
    {
        return $this->hasMany(PostInflow::class);
    }

    public function outflows()
    {
        return $this->hasMany(PostOutflow::class);
    }

    public function creditTransactions()
    {
        return $this->hasMany(CreditTransaction::class,"customer_id");
    }
    
}
