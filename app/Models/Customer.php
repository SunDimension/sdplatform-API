<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
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
        'surname',
        'firstname',
        'middlename',
        'phone_number',
            'name',
        'email',
        'address',
        'credit_limit'
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
        'credit_limit'=>'integer'
    ];

    public function customerType(): BelongsTo
    {
        return $this->belongsTo(CustomerType::class);
    }

    public function title(): BelongsTo
    {
        return $this->belongsTo(Title::class);
    }

      public function creditlimit(): BelongsTo
    {
        return $this->belongsTo(CreditLimit::class);
    }
}
