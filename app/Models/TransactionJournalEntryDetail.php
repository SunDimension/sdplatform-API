<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Concerns\Syncable;
class TransactionJournalEntryDetail extends Model
{
    use HasFactory, HasUuids, SoftDeletes, Syncable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'transaction_journal_entry_id',
        'journal_type_id',
        'amount',
        'description',
        'account_id',
        'account_no',
        'created_by',
        'modified_by',
        'deleted_by',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'amount' => 'double',
        'created_by' => 'integer',
        'modified_by' => 'integer',
        'deleted_by' => 'integer',
    ];

    public function transactionJournalEntry(): BelongsTo
    {
        return $this->belongsTo(TransactionJournalEntry::class);
    }

    public function journalType(): BelongsTo
    {
        return $this->belongsTo(JournalType::class);
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class );
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class,'created_by');
    }

    public function modifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class,'modified_by');
    }

    public function deletedBy(): BelongsTo
    {
        return $this->belongsTo(User::class,'deleted_by');
    }
} 