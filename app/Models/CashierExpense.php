<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\HasUuid;
use App\Models\Concerns\Syncable;
use Illuminate\Support\Str;

class CashierExpense extends Model
{
    use HasFactory, SoftDeletes, Syncable, HasUuid;


    public $incrementing = false;
    protected $keyType = 'string';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'account_id',
        'branch_id',
        'amount',
        'user_id',
        'store_id',
        'date',
        'approved_by',
        'approval_date',
        'approval_comment',
        'status',
        'narration',
        'sync_id',
        'location_id',
        'sync_status',
        'sync_version',
        'last_synced_at',
        'last_sync_attempt_at',
        'sync_error',
        'id',
        'payment_method',

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
        'store_id' => 'string',
        'account_id' => 'string',
        'approved_by' => 'string',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            // Generate UUID for id if not set
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }
        });
    }

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

    public function account()
    {
        return $this->belongsTo(LedgerAccount::class, 'account_id');
    }

    public function expense()
    {
        return $this->belongsTo(ExpenseLine::class, 'expense_line_id');
    }
}
