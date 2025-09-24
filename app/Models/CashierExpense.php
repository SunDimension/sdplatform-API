<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasUuid;
use App\Models\Concerns\Syncable;
class CashierExpense extends Model
{
    use HasFactory, HasUuid, Syncable;

    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'expense_line_id',
        'branch_id',
        'amount',
        'user_id',
        'store_id',
        'date',
        'approved_by',
        'approval_date',
        'approval_comment',
        'status',
        'narration'

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
        'expense_line_id' => 'string',
        'approved_by' => 'integer',
    ];

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

    public function expense()
    {
        return $this->belongsTo(ExpenseLine::class,'expense_line_id');
    }
}
