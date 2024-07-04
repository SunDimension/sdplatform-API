<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class JournalEntryDetail extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'journal_entry_id',
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
        'journal_entry_id' => 'integer',
        'journal_type_id' => 'integer',
        'amount' => 'double',
        'deleted_by' => 'integer',
    ];

    public function journalEntry(): BelongsTo
    {
        return $this->belongsTo(JournalEntry::class);
    }

    public function journalType(): BelongsTo
    {
        return $this->belongsTo(JournalType::class);
    }

    public function deletedBy(): BelongsTo
    {
        return $this->belongsTo(Employees,id::class);
    }
}
