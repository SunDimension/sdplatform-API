<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasUuid;
use App\Models\Concerns\Syncable;
class PostOutflow extends Model
{
    use HasFactory, HasUuid, Syncable;
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [

        "org_bank",
        // "beneficiary",
        "amount",
        "account_name",
        "account_number",
        "bene_bank",
        "narration",
        "outflow_date",
        "outflow_mode",
        "customer_id",
        "sales_receipt_id"
    ];

    protected $casts = [
        "id" => "string",
        "org_bank" => "integer",
        "bene_bank" => "integer",
        "customer_id" => "string",
        "sales_receipt_id" => "string"
    ];

    public function bank(): BelongsTo
    {
        return $this->belongsTo(Bank::class);
    }

    // Outflow.php
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }
    protected $appends = ['error', 'message'];

    public function getErrorAttribute()
    {
        return $this->attributes['error'] ?? false;
    }

    public function getMessageAttribute()
    {
        return $this->attributes['message'] ?? null;
    }
}
