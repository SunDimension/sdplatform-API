<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentVoucher extends Model
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
        'product_id',
        'expense_date',
        'amount',
        'description',
        'branch_id',
        'warehouse_id',
        'tax_id',
        'vendor_id',
        'payment_mode_id',
        'expense_account_id',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'string',
        // 'product_id' => 'string',
        'expense_date' => 'timestamp',
        'branch_id' => 'string',
        'warehouse_id' => 'string',
        'tax_id' => 'string',
        'vendor_id' => 'string',
        'payment_mode_id' => 'string',
        // 'expense_account_id' => 'string',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(CreateItem::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function tax(): BelongsTo
    {
        return $this->belongsTo(Tax::class);
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    public function paymentMode(): BelongsTo
    {
        return $this->belongsTo(PaymentMode::class);
    }

    public function expenseAccount(): BelongsTo
    {
        return $this->belongsTo(Account::class,'expense_account_id');
    }
}
