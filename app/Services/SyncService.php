<?php

namespace App\Services;

use App\Models\SyncQueue;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Exception;
use App\Services\SyncNotificationService;

class SyncService
{
    protected $centralHubUrl;
    protected $locationId;
    protected $apiKey;
    protected $notificationService;

    public function __construct(SyncNotificationService $notificationService)
    {
        $this->centralHubUrl = config('sync.central_hub_url');
        $this->locationId = config('app.location_id');
        $this->apiKey = config('sync.api_key');
        $this->notificationService = $notificationService;
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
                    
                    // Send notification for critical failures
                    if ($this->shouldNotifyForFailure($e->getMessage())) {
                        $this->notificationService->notifySyncFailure([
                            'model' => get_class($model),
                            'id' => $model->id,
                            'error' => $e->getMessage(),
                            'operation' => 'push'
                        ], 'push');
                    }
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
            'errors' => [],
            'data' => [],
            'meta' => []
        ];

        try {
            // Check if this location IS the central hub
            if ($this->isCentralHub()) {
                Log::info('Central hub detected - collecting local sync data for distribution');
                
                // Central hub collects its own sync data instead of making HTTP calls
                $centralHubData = $this->getCentralHubSyncData();
                $results['data'] = $centralHubData['data'];
                $results['meta'] = $centralHubData['meta'];
                
                Log::info('Central hub sync data collected', [
                    'total_changes' => count($results['data']),
                    'meta' => $results['meta']
                ]);
                
                return $results;
            }

            // Debug: Log the URL being used
            Log::info('Sync pull URL', ['url' => $this->centralHubUrl . '/api/sync/pull']);
            
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'X-Location-ID' => $this->locationId,
            ])->post($this->centralHubUrl . '/api/sync/pull');

            if ($response->successful()) {
                $responseData = $response->json();
                
                // Extract the actual sync data from the response
                $changes = $responseData['data'] ?? [];
                $meta = $responseData['meta'] ?? [];
                
                // Store the raw data for reference
                $results['data'] = $changes;
                $results['meta'] = $meta;
                
                Log::info('Received changes from central hub', [
                    'total_changes' => count($changes),
                    'meta' => $meta
                ]);
                
                // Process each change
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
     * Pull changes for central hub (incoming pull requests)
     * This method is called by the central hub to retrieve local changes
     * Central hub then distributes these changes to other locations
     */
    public function pullChangesForHub(string $modelType = null, int $limit = 100): array
    {
        $results = [
            'success' => true,
            'data' => [],
            'meta' => [
                'location_id' => $this->locationId,
                'timestamp' => now()->toISOString(),
                'total_count' => 0,
                'returned_count' => 0
            ]
        ];

        try {
            // Get models that need syncing
            $models = $this->getModelsNeedingSync($modelType);
            $results['meta']['total_count'] = count($models);
            
            // Limit the results
            $models = array_slice($models, 0, $limit);
            $results['meta']['returned_count'] = count($models);
            
            foreach ($models as $model) {
                try {
                    $syncData = $this->prepareModelForHub($model);
                    $results['data'][] = $syncData;
                } catch (Exception $e) {
                    Log::error('Failed to prepare model for hub', [
                        'model' => get_class($model),
                        'id' => $model->id,
                        'error' => $e->getMessage()
                    ]);
                    
                    // Continue with other models
                    continue;
                }
            }
            
            Log::info('Central hub pull request processed', [
                'location_id' => $this->locationId,
                'total_models' => $results['meta']['total_count'],
                'returned_models' => $results['meta']['returned_count']
            ]);
            
        } catch (Exception $e) {
            Log::error('Central hub pull request failed', [
                'location_id' => $this->locationId,
                'error' => $e->getMessage()
            ]);
            
            $results['success'] = false;
            $results['error'] = $e->getMessage();
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
     * Resolve model class name to full class path
     */
    protected function resolveModelClass(string $modelName): ?string
    {
        // If it's already a full class name, return it
        if (class_exists($modelName)) {
            return $modelName;
        }
        
        // Try to resolve from common model names
        $possibleClasses = [
            'App\\Models\\' . $modelName,
            'App\\Models\\' . ucfirst($modelName),
            'App\\Models\\' . ucfirst($modelName),
        ];
        
        foreach ($possibleClasses as $class) {
            if (class_exists($class)) {
                return $class;
            }
        }
        
        return null;
    }

    /**
     * Get models that need synchronization
     */
    protected function getModelsNeedingSync(string $modelType = null): array
    {
        $models = [];
        
        if ($modelType) {
            $modelClass = $this->resolveModelClass($modelType);
            if ($modelClass) {
                $models = $modelClass::whereIn('sync_status', ['pending', 'deleted_pending'])->get()->all();
            }
        } else {
            // Get all syncable models
            $syncableModels = $this->getSyncableModels();
            foreach ($syncableModels as $modelClass) {
                $pendingModels = $modelClass::whereIn('sync_status', ['pending', 'deleted_pending'])->get();
                $models = array_merge($models, $pendingModels->all());
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
     * Check if notification should be sent for this failure
     */
    protected function shouldNotifyForFailure(string $errorMessage): bool
    {
        // Don't notify for common/expected errors
        $ignoredErrors = [
            'cURL error 6: Could not resolve host',
            '508 Resource Limit Is Reached',
            'Connection timed out',
            'Network is unreachable'
        ];
        
        foreach ($ignoredErrors as $ignoredError) {
            if (str_contains($errorMessage, $ignoredError)) {
                return false;
            }
        }
        
        return true;
    }

    /**
     * Check if system is online
     */
    public function isOnline(): bool
    {
        try {
            $response = Http::timeout(5)->get($this->centralHubUrl . '/api/sync/status');
            return $response->successful();
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Check if this location IS the central hub
     */
    public function isCentralHub(): bool
    {
        // Method 1: Check if this location is configured as the central hub
        if (config('sync.is_central_hub', false)) {
            return true;
        }
        
        // Method 2: Check if current host matches central hub host
        $currentHost = request()->getHost();
        $hubHost = parse_url($this->centralHubUrl, PHP_URL_HOST);
        
        if (strtolower($currentHost) === strtolower($hubHost)) {
            return true;
        }
        
        // Method 3: Check if current location ID matches central hub location ID
        $centralHubLocationId = config('sync.central_hub_location_id');
        if ($centralHubLocationId && $this->locationId == $centralHubLocationId) {
            return true;
        }
        
        return false;
    }

    /**
     * Get sync data from central hub's own database
     * This method is called when the central hub needs to provide sync data
     */
    protected function getCentralHubSyncData(): array
    {
        $results = [
            'data' => [],
            'meta' => [
                'location_id' => $this->locationId,
                'timestamp' => now()->toISOString(),
                'total_count' => 0,
                'returned_count' => 0,
                'source' => 'central_hub_local'
            ]
        ];

        try {
            // Get models that need syncing from central hub's database
            $models = $this->getModelsNeedingSync();
            $results['meta']['total_count'] = count($models);
            
            // Limit results to prevent overwhelming responses
            $limit = config('sync.batch_size', 100);
            $models = array_slice($models, 0, $limit);
            $results['meta']['returned_count'] = count($models);
            
            foreach ($models as $model) {
                try {
                    $syncData = $this->prepareModelForHub($model);
                    $results['data'][] = $syncData;
                } catch (Exception $e) {
                    Log::error('Failed to prepare central hub model for sync', [
                        'model' => get_class($model),
                        'id' => $model->id,
                        'error' => $e->getMessage()
                    ]);
                    
                    // Continue with other models
                    continue;
                }
            }
            
            Log::info('Central hub sync data prepared', [
                'total_models' => $results['meta']['total_count'],
                'returned_models' => $results['meta']['returned_count']
            ]);
            
        } catch (Exception $e) {
            Log::error('Failed to get central hub sync data', [
                'error' => $e->getMessage()
            ]);
            
            // Return empty results on error
            $results['data'] = [];
            $results['meta']['error'] = $e->getMessage();
        }

        return $results;
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
     * Prepare model data for central hub consumption
     */
    protected function prepareModelForHub($model): array
    {
        $syncData = [
            'model_type' => get_class($model),
            'model_id' => $model->id,
            'sync_id' => $model->sync_id,
            'location_id' => $model->location_id,
            'sync_version' => $model->sync_version ?? 1,
            'sync_status' => $model->sync_status,
            'action' => $this->determineModelAction($model),
            'data' => $model->getSyncData(),
            'metadata' => [
                'created_at' => $model->created_at?->toISOString(),
                'updated_at' => $model->updated_at?->toISOString(),
                'last_synced_at' => $model->last_synced_at?->toISOString(),
                'last_sync_attempt_at' => $model->last_sync_attempt_at?->toISOString(),
            ]
        ];

        // Add soft delete information if applicable
        if (method_exists($model, 'trashed') && $model->trashed()) {
            $syncData['action'] = 'delete';
            $syncData['deleted_at'] = $model->deleted_at?->toISOString();
        }

        return $syncData;
    }

    /**
     * Determine the action for a model based on its sync status
     */
    protected function determineModelAction($model): string
    {
        if (method_exists($model, 'trashed') && $model->trashed()) {
            return 'delete';
        }
        
        if ($model->sync_status === 'deleted_pending') {
            return 'delete';
        }
        
        if ($model->wasRecentlyCreated) {
            return 'create';
        }
        
        return 'update';
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