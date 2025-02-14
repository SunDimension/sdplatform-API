<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentVoucher extends Model
{
    use HasFactory;

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
        'id' => 'integer',
        // 'product_id' => 'integer',
        'expense_date' => 'timestamp',
        'branch_id' => 'integer',
        'warehouse_id' => 'integer',
        'tax_id' => 'integer',
        'vendor_id' => 'integer',
        'payment_mode_id' => 'integer',
        // 'expense_account_id' => 'integer',
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
