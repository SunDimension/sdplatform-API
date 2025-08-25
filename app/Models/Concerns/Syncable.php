<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Carbon\Carbon;

trait Syncable
{
    /**
     * Boot the trait and add necessary columns
     */
    protected static function bootSyncable()
    {
        static::creating(function (Model $model) {
            if (!$model->sync_id) {
                $model->sync_id = Str::uuid();
            }
            if (!$model->location_id) {
                $model->location_id = config('app.location_id');
            }
            $model->sync_status = 'pending';
            $model->last_synced_at = null;
            $model->sync_version = 1;
        });

        static::updating(function (Model $model) {
            // Only update sync status if it's not being explicitly set to 'synced'
            // and if this is not a sync-related update (to avoid infinite loops)
            if ($model->sync_status !== 'synced' && 
                !$model->isDirty('sync_status') && 
                !$model->isDirty('last_synced_at')) {
                
                $model->sync_status = 'pending';
                $model->sync_version = ($model->sync_version ?? 0) + 1;
            }
        });

        static::updated(function (Model $model) {
            // Fallback: if the updating event didn't work, check if we need to mark as pending
            // This ensures that any model update (except sync-related ones) gets marked as pending
            if ($model->sync_status === 'synced' && 
                !$model->isDirty('sync_status') && 
                !$model->isDirty('last_synced_at') &&
                !$model->isDirty('sync_version')) {
                
                // Force update the sync status to pending
                $model->update([
                    'sync_status' => 'pending',
                    'sync_version' => ($model->sync_version ?? 0) + 1,
                ]);
            }
        });

        static::deleting(function (Model $model) {
            // Mark as deleted for sync instead of actually deleting
            $model->sync_status = 'deleted_pending';
            $model->sync_version = ($model->sync_version ?? 0) + 1;
            $model->save();
            return false; // Prevent actual deletion
        });
    }

    /**
     * Get the sync ID for this record
     */
    public function getSyncId(): string
    {
        return $this->sync_id;
    }

    /**
     * Get the location ID where this record was created
     */
    public function getLocationId(): string
    {
        return $this->location_id;
    }

    /**
     * Check if this record needs synchronization
     */
    public function needsSync(): bool
    {
        return in_array($this->sync_status, ['pending', 'deleted_pending']);
    }

    /**
     * Mark record as synced
     */
    public function markAsSynced(): void
    {
        // Use direct property assignment to avoid triggering updating event
        $this->sync_status = 'synced';
        $this->last_synced_at = now();
        $this->saveQuietly(); // Save without triggering events
    }

    /**
     * Mark record as sync failed
     */
    public function markAsSyncFailed(string $error = null): void
    {
        $this->update([
            'sync_status' => 'failed',
            'sync_error' => $error,
            'last_sync_attempt_at' => now(),
        ]);
    }

    /**
     * Mark record as needing sync (for testing and manual operations)
     */
    public function markAsNeedingSync(): void
    {
        $this->update([
            'sync_status' => 'pending',
            'sync_version' => ($this->sync_version ?? 0) + 1,
        ]);
    }

    /**
     * Get sync metadata for transmission
     */
    public function getSyncData(): array
    {
        $data = $this->toArray();
        $data['sync_metadata'] = [
            'sync_id' => $this->sync_id,
            'location_id' => $this->location_id,
            'sync_version' => $this->sync_version,
            'sync_status' => $this->sync_status,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
            'deleted_at' => $this->deleted_at?->toISOString(),
        ];
        
        return $data;
    }

    /**
     * Scope to get records that need synchronization
     */
    public function scopeNeedsSync($query)
    {
        return $query->whereIn('sync_status', ['pending', 'deleted_pending']);
    }

    /**
     * Scope to get records by location
     */
    public function scopeByLocation($query, string $locationId)
    {
        return $query->where('location_id', $locationId);
    }

    /**
     * Scope to get records by sync status
     */
    public function scopeBySyncStatus($query, string $status)
    {
        return $query->where('sync_status', $status);
    }
} 