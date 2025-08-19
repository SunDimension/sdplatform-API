<?php

namespace App\Services;

use App\Models\SyncQueue;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Exception;

class SyncService
{
    protected $centralHubUrl;
    protected $locationId;
    protected $apiKey;

    public function __construct()
    {
        $this->centralHubUrl = config('sync.central_hub_url');
        $this->locationId = config('app.location_id');
        $this->apiKey = config('sync.api_key');
    }

    /**
     * Push local changes to central hub
     */
    public function pushChanges(string $modelType = null): array
    {
        $results = [
            'success' => 0,
            'failed' => 0,
            'errors' => []
        ];

        try {
            // Get all models that need syncing
            $models = $this->getModelsNeedingSync($modelType);
            
            foreach ($models as $model) {
                try {
                    $this->pushModelToHub($model);
                    $results['success']++;
                } catch (Exception $e) {
                    $results['failed']++;
                    $results['errors'][] = [
                        'model' => get_class($model),
                        'id' => $model->id,
                        'error' => $e->getMessage()
                    ];
                    Log::error('Sync push failed', [
                        'model' => get_class($model),
                        'id' => $model->id,
                        'error' => $e->getMessage()
                    ]);
                }
            }
        } catch (Exception $e) {
            Log::error('Sync push process failed', ['error' => $e->getMessage()]);
            throw $e;
        }

        return $results;
    }

    /**
     * Pull changes from central hub
     */
    public function pullChanges(): array
    {
        $results = [
            'success' => 0,
            'failed' => 0,
            'errors' => []
        ];

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'X-Location-ID' => $this->locationId,
            ])->get($this->centralHubUrl . '/api/sync/pull');

            if ($response->successful()) {
                $changes = $response->json('data', []);
                
                foreach ($changes as $change) {
                    try {
                        $this->applyChangeFromHub($change);
                        $results['success']++;
                    } catch (Exception $e) {
                        $results['failed']++;
                        $results['errors'][] = [
                            'change' => $change,
                            'error' => $e->getMessage()
                        ];
                        Log::error('Sync pull failed', [
                            'change' => $change,
                            'error' => $e->getMessage()
                        ]);
                    }
                }
            } else {
                throw new Exception('Failed to pull changes: ' . $response->body());
            }
        } catch (Exception $e) {
            Log::error('Sync pull process failed', ['error' => $e->getMessage()]);
            throw $e;
        }

        return $results;
    }

    /**
     * Process sync queue (for offline scenarios)
     */
    public function processQueue(): array
    {
        $results = [
            'processed' => 0,
            'failed' => 0,
            'errors' => []
        ];

        $pendingItems = SyncQueue::pending()->limit(50)->get();

        foreach ($pendingItems as $item) {
            try {
                $item->markAsProcessing();
                
                if ($this->processQueueItem($item)) {
                    $item->markAsCompleted();
                    $results['processed']++;
                } else {
                    $item->markAsFailed('Processing failed');
                    $results['failed']++;
                }
            } catch (Exception $e) {
                $item->markAsFailed($e->getMessage());
                $results['failed']++;
                $results['errors'][] = [
                    'item_id' => $item->id,
                    'error' => $e->getMessage()
                ];
            }
        }

        return $results;
    }

    /**
     * Add item to sync queue
     */
    public function addToQueue($model, string $action = 'update'): SyncQueue
    {
        return SyncQueue::create([
            'sync_id' => $model->getSyncId(),
            'model_type' => get_class($model),
            'model_id' => $model->id,
            'action' => $action,
            'data' => $model->getSyncData(),
            'location_id' => $this->locationId,
            'priority' => $this->getPriority($model),
            'status' => 'pending',
            'next_attempt_at' => now(),
        ]);
    }

    /**
     * Resolve conflicts between local and remote data
     */
    protected function resolveConflict($localModel, array $remoteData): bool
    {
        $localVersion = $localModel->sync_version ?? 0;
        $remoteVersion = $remoteData['sync_metadata']['sync_version'] ?? 0;
        
        // Simple conflict resolution: newer version wins
        if ($remoteVersion > $localVersion) {
            $this->updateLocalModel($localModel, $remoteData);
            return true;
        }
        
        return false;
    }

    /**
     * Get models that need synchronization
     */
    protected function getModelsNeedingSync(string $modelType = null): array
    {
        $models = [];
        
        if ($modelType) {
            $models[] = $modelType::needsSync()->get();
        } else {
            // Get all syncable models
            $syncableModels = $this->getSyncableModels();
            foreach ($syncableModels as $modelClass) {
                $models = array_merge($models, $modelClass::needsSync()->get()->toArray());
            }
        }
        
        return $models;
    }

    /**
     * Push model to central hub
     */
    protected function pushModelToHub($model): bool
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
            'X-Location-ID' => $this->locationId,
        ])->post($this->centralHubUrl . '/api/sync/push', [
            'model_type' => get_class($model),
            'data' => $model->getSyncData(),
        ]);

        if ($response->successful()) {
            $model->markAsSynced();
            return true;
        }

        throw new Exception('Push failed: ' . $response->body());
    }

    /**
     * Apply change from central hub
     */
    protected function applyChangeFromHub(array $change): bool
    {
        $modelType = $change['model_type'];
        $data = $change['data'];
        $action = $change['action'];

        DB::beginTransaction();
        try {
            switch ($action) {
                case 'create':
                    $this->createLocalModel($modelType, $data);
                    break;
                case 'update':
                    $this->updateLocalModel($modelType, $data);
                    break;
                case 'delete':
                    $this->deleteLocalModel($modelType, $data);
                    break;
            }
            
            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Process queue item
     */
    protected function processQueueItem(SyncQueue $item): bool
    {
        try {
            if ($this->isOnline()) {
                return $this->pushModelToHub($item->getModelInstance());
            } else {
                // Store in queue for later processing
                return true;
            }
        } catch (Exception $e) {
            Log::error('Queue item processing failed', [
                'item_id' => $item->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Check if system is online
     */
    protected function isOnline(): bool
    {
        try {
            $response = Http::timeout(5)->get($this->centralHubUrl . '/health');
            return $response->successful();
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Get priority for sync queue
     */
    protected function getPriority($model): int
    {
        // Higher priority for critical models
        $criticalModels = [
            'App\Models\JournalEntry',
            'App\Models\Transaction',
            'App\Models\PaymentVoucher'
        ];
        
        return in_array(get_class($model), $criticalModels) ? 10 : 5;
    }

    /**
     * Get all syncable models
     */
    protected function getSyncableModels(): array
    {
        return [
            'App\Models\JournalEntry',
            'App\Models\Transaction',
            'App\Models\PaymentVoucher',
            'App\Models\Customer',
            'App\Models\Vendor',
            'App\Models\Store',
            'App\Models\Branch',
            // Add more models as needed
        ];
    }

    /**
     * Create local model from remote data
     */
    protected function createLocalModel(string $modelType, array $data): void
    {
        $model = new $modelType();
        $this->fillModelFromData($model, $data);
        $model->save();
    }

    /**
     * Update local model from remote data
     */
    protected function updateLocalModel(string $modelType, array $data): void
    {
        $model = $modelType::where('sync_id', $data['sync_metadata']['sync_id'])->first();
        
        if (!$model) {
            $this->createLocalModel($modelType, $data);
            return;
        }
        
        $this->fillModelFromData($model, $data);
        $model->save();
    }

    /**
     * Delete local model
     */
    protected function deleteLocalModel(string $modelType, array $data): void
    {
        $model = $modelType::where('sync_id', $data['sync_metadata']['sync_id'])->first();
        
        if ($model) {
            $model->delete();
        }
    }

    /**
     * Fill model with data from remote
     */
    protected function fillModelFromData($model, array $data): void
    {
        $syncMetadata = $data['sync_metadata'] ?? [];
        unset($data['sync_metadata']);
        
        foreach ($data as $key => $value) {
            if ($model->isFillable($key)) {
                $model->$key = $value;
            }
        }
        
        // Preserve sync metadata
        $model->sync_id = $syncMetadata['sync_id'] ?? $model->sync_id;
        $model->location_id = $syncMetadata['location_id'] ?? $model->location_id;
        $model->sync_version = $syncMetadata['sync_version'] ?? $model->sync_version;
        $model->sync_status = 'synced';
        $model->last_synced_at = now();
    }
} 