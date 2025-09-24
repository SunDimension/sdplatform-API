<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasUuid;
use App\Models\Concerns\Syncable;
class PostInflow extends Model
{
    use HasFactory, HasUuid, Syncable;

    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $table = "post_inflows";

    protected $fillable =[

        "bank_id",
        "amount",
        "narration",
        "inflow_date",
        "inflow_status",
        'customer_id'
    ];

    protected $casts =[
        "id"=>"integer",
        "bank_id"=>"integer",
        "inflow_status"=>"integer",
        "customer_id"=>"integer"
    ];

    public function bank(): BelongsTo
{
    return $this->belongsTo(Bank::class);
}

       public function inflowStatus(): BelongsTo
    {
        return $this->belongsTo(InflowStatus::class);

    }

       public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }
}
