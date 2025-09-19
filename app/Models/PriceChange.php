<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PriceChange extends Model
{
    use HasFactory, HasUuid;

    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'details',
        'store_id',
        'branch_id',
        'change_reason_id',
        'status',
        'approved_by',
        'approval_date',
        'comment',
        'created_by',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'string',
        'details' => 'array',
        // 'approval_date' => 'timestamp',
        // 'created_at' => 'timestamp',
        // 'updated_at' => 'timestamp',
    ];

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    public function changeReason(): BelongsTo
    {
        return $this->belongsTo(ChangeReason::class);
    }
}
