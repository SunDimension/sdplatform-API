<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasUuid;
use App\Models\Concerns\Syncable;
use Illuminate\Support\Str;

class BranchDisbursedStock extends Model
{
    use HasFactory, HasUuid, Syncable;

    protected $table = 'branch_disbursed_stocks';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'branch_id',
        'product_id',
        'disbursement_id',
        'quantity_disbursed',
        'quantity_received',
        'quantity_available',
        // 'sync_id',
        'location_id',
        'sync_status',
        'sync_version',
        'last_synced_at',
        'last_sync_attempt_at',
        'sync_error',
        'sync_id'
    ];

    protected $casts = [
        'quantity_disbursed' => 'decimal:2',
        'quantity_received' => 'decimal:2',
        'quantity_available' => 'decimal:2',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function product()
    {
        return $this->belongsTo(GoodsRecieved::class);
    }

    public function disbursement()
    {
        return $this->belongsTo(StockDisbursement::class, 'disbursement_id', 'disbursement_id');
    }
}
