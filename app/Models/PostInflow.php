<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class PostInflow extends Model
{
    use HasFactory;

    protected $table = "post_inflows";

    protected $fillable =[

        "bank_id",
        "amount",
        "narration",
        "inflow_date",
        "inflow_status",
    ];

    protected $casts =[

        "id"=>"integer",
        "bank_id"=>"integer",
        "inflow_status"=>"integer"
    ];

    public function bank(): BelongsTo
{
    return $this->belongsTo(Bank::class);
}

       public function inflowStatus(): BelongsTo
    {
        return $this->belongsTo(InflowStatus::class);
    }
}
