<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SyncQueue extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'sync_id',
        'model_type',
        'model_id',
        'action', // create, update, delete
        'data',
        'location_id',
        'priority',
        'retry_count',
        'last_attempt_at',
        'next_attempt_at',
        'status', // pending, processing, failed, completed
        'error_message',
        'sync_batch_id',
    ];

    protected $casts = [
        'data' => 'array',
        'last_attempt_at' => 'datetime',
        'next_attempt_at' => 'datetime',
        'retry_count' => 'integer',
        'priority' => 'integer',
    ];

    /**
     * Get the model instance this queue item represents
     */
    public function getModelInstance()
    {
        if (!$this->model_type || !$this->model_id) {
            return null;
        }

        return $this->model_type::find($this->model_id);
    }

    /**
     * Scope to get pending sync items
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending')
                    ->where('next_attempt_at', '<=', now())
                    ->orderBy('priority', 'desc')
                    ->orderBy('created_at', 'asc');
    }

    /**
     * Scope to get failed sync items
     */
    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    /**
     * Mark as processing
     */
    public function markAsProcessing(): void
    {
        $this->update([
            'status' => 'processing',
            'last_attempt_at' => now(),
        ]);
    }

    /**
     * Mark as completed
     */
    public function markAsCompleted(): void
    {
        $this->update([
            'status' => 'completed',
            'last_attempt_at' => now(),
        ]);
    }

    /**
     * Mark as failed with retry logic
     */
    public function markAsFailed(string $error, int $maxRetries = 3): void
    {
        $this->retry_count++;
        
        if ($this->retry_count >= $maxRetries) {
            $this->update([
                'status' => 'failed',
                'error_message' => $error,
                'last_attempt_at' => now(),
            ]);
        } else {
            // Exponential backoff: 1min, 5min, 15min
            $delay = pow(3, $this->retry_count - 1);
            $this->update([
                'status' => 'pending',
                'error_message' => $error,
                'last_attempt_at' => now(),
                'next_attempt_at' => now()->addMinutes($delay),
            ]);
        }
    }

    /**
     * Reset for retry
     */
    public function resetForRetry(): void
    {
        $this->update([
            'status' => 'pending',
            'error_message' => null,
            'next_attempt_at' => now(),
        ]);
    }
} 