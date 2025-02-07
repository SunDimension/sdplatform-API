<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class SettleCredit extends Model
{
    use HasFactory;


        protected $fillable =[
            "customer_id",
            "credit_limit",
            "current_debt",
            "settlement_amount",
            "settlement_date",
        ];

        protected $cast =[
            "id"=>"integer",
            "customer_id"=>"integer"
        
        ];
    
       public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }
}
