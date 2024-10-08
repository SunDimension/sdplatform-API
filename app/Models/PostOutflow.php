<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class PostOutflow extends Model
{
    use HasFactory;

    protected $fillable = [
        
        "org_bank",
        "beneficiary",
        "amount",
        "account_name",
        "account_number",
        "bene_bank",
        "narration",
        "outflow_date",
        "outflow_mode"
    
    ];

    protected $cast =[
        "id"=>"integer",
        "org_bank"=>"integer",
        "bene_bank"=>"integer"
    ];

          public function bank(): BelongsTo
    {
        return $this->belongsTo(Bank::class);
    }
}
