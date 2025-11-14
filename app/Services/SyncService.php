<?php

namespace App\Services;

use App\Models\SyncQueue;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Exception;
use App\Services\SyncNotificationService;
use Illuminate\Database\Eloquent\Model as EloquentModel;

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
     * Process incoming sync data from other locations (for central hub)
     */
    public function processIncomingSyncData(string $modelType, array $syncData): array
    {
        $results = [
            'success' => 0,
            'failed' => 0,
            'errors' => []
        ];

        try {
            Log::info('Processing incoming sync data', [
                'model_type' => $modelType,
                'data_size' => count($syncData)
            ]);

            // Extract sync metadata
            $syncMetadata = $syncData['sync_metadata'] ?? [];
            $modelData = array_diff_key($syncData, ['sync_metadata' => '']);

            // Determine the action with better semantics across locations
            $action = $syncMetadata['action'] ?? null;
            if ($action === null) {
                if (($syncMetadata['sync_status'] ?? null) === 'deleted_pending') {
                    $action = 'delete';
                } else {
                    $syncId = $syncMetadata['sync_id'] ?? null;
                    if (!$syncId) {
                        // Without sync_id, treat as create
                        $action = 'create';
                    } else {
                        // If record with sync_id exists locally on hub, update; otherwise create
                        $exists = class_exists($modelType) ? (bool) $modelType::where('sync_id', $syncId)->exists() : false;
                        $action = $exists ? 'update' : 'create';
                    }
                }
            }

            // Process based on action
            switch ($action) {
                case 'create':
                    $this->createModelFromSyncData($modelType, $modelData, $syncMetadata);
                    $results['success']++;
                    break;

                case 'update':
                    $this->updateModelFromSyncData($modelType, $modelData, $syncMetadata);
                    $results['success']++;
                    break;

                case 'delete':
                    $this->deleteModelFromSyncData($modelType, $syncMetadata);
                    $results['success']++;
                    break;

                default:
                    $results['failed']++;
                    $results['errors'][] = "Unknown action: {$action}";
            }

            Log::info('Incoming sync data processed', [
                'action' => $action,
                'success' => $results['success'],
                'failed' => $results['failed']
            ]);
        } catch (Exception $e) {
            $results['failed']++;
            $results['errors'][] = $e->getMessage();
            Log::error('Failed to process incoming sync data', [
                'model_type' => $modelType,
                'error' => $e->getMessage()
            ]);
        }

        return $results;
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
            // Debug: Log push operation start
            Log::info('Starting push operation', [
                'model_type' => $modelType,
                'central_hub_url' => $this->centralHubUrl,
                'location_id' => $this->locationId,
                'is_central_hub' => $this->isCentralHub()
            ]);

            // Check if this IS the central hub
            if ($this->isCentralHub()) {
                Log::info('Central hub detected - processing local models for sync');

                // Central hub processes its own models locally
                $models = $this->getModelsNeedingSync($modelType);

                Log::info('Central hub models found for syncing', [
                    'total_models' => count($models),
                    'model_types' => array_map('get_class', $models)
                ]);

                foreach ($models as $model) {
                    try {
                        Log::info('Central hub processing local model', [
                            'model_class' => get_class($model),
                            'model_id' => $model->id,
                            'sync_id' => $model->sync_id ?? 'none'
                        ]);

                        // For central hub, just mark as synced (no external push needed)
                        $model->markAsSynced();
                        $results['success']++;

                        Log::info('Central hub model marked as synced', [
                            'model_class' => get_class($model),
                            'model_id' => $model->id
                        ]);
                    } catch (Exception $e) {
                        $results['failed']++;
                        $results['errors'][] = [
                            'model' => get_class($model),
                            'id' => $model->id,
                            'error' => $e->getMessage()
                        ];
                        Log::error('Central hub sync failed', [
                            'model' => get_class($model),
                            'id' => $model->id,
                            'error' => $e->getMessage()
                        ]);
                    }
                }

                Log::info('Central hub push operation completed', [
                    'success' => $results['success'],
                    'failed' => $results['failed'],
                    'total' => count($models)
                ]);

                return $results;
            }

            // Regular location: push to external central hub
            Log::info('Regular location detected - pushing to external central hub');

            // Get all models that need syncing
            $models = $this->getModelsNeedingSync($modelType);

            Log::info('Models found for syncing', [
                'total_models' => count($models),
                'model_types' => array_map('get_class', $models)
            ]);

            foreach ($models as $model) {
                try {
                    Log::info('Pushing model to central hub', [
                        'model_class' => get_class($model),
                        'model_id' => $model->id,
                        'sync_id' => $model->sync_id ?? 'none'
                    ]);

                    $this->pushModelToHub($model);
                    $results['success']++;

                    Log::info('Model pushed successfully', [
                        'model_class' => get_class($model),
                        'model_id' => $model->id
                    ]);
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

            Log::info('Regular location push operation completed', [
                'success' => $results['success'],
                'failed' => $results['failed'],
                'total' => count($models)
            ]);
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
            // Debug: Log central hub detection
            Log::info('Central hub detection check', [
                'is_central_hub' => $this->isCentralHub(),
                'config_sync_is_central_hub' => config('sync.is_central_hub'),
                'current_host' => request()->getHost(),
                'hub_host' => parse_url($this->centralHubUrl, PHP_URL_HOST),
                'location_id' => $this->locationId,
                'central_hub_location_id' => config('sync.central_hub_location_id')
            ]);

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

                // Central hub doesn't process changes - it only provides data
                // So we return the data without processing
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

                // Debug: Log the structure of changes
                Log::info('Changes array structure', [
                    'changes_count' => count($changes),
                    'changes_type' => gettype($changes),
                    'first_change' => $changes[0] ?? 'no_changes',
                    'first_change_type' => isset($changes[0]) ? gettype($changes[0]) : 'n/a'
                ]);

                // Process each change
                foreach ($changes as $change) {
                    try {
                        // Debug: Log each change
                        Log::info('Processing change', [
                            'change' => $change,
                            'change_type' => gettype($change)
                        ]);

                        // Validate change structure before processing
                        if (!is_array($change)) {
                            Log::warning('Invalid change structure received from central hub', [
                                'change' => $change,
                                'change_type' => gettype($change),
                                'expected_type' => 'array'
                            ]);

                            $results['failed']++;
                            $results['errors'][] = [
                                'change' => $change,
                                'error' => 'Invalid change structure: expected array, got ' . gettype($change)
                            ];
                            continue; // Skip this invalid change
                        }

                        // Validate required fields
                        if (!isset($change['model_type']) || !isset($change['action']) || !isset($change['data'])) {
                            Log::warning('Change missing required fields', [
                                'change' => $change,
                                'missing_fields' => array_diff(['model_type', 'action', 'data'], array_keys($change))
                            ]);

                            $results['failed']++;
                            $results['errors'][] = [
                                'change' => $change,
                                'error' => 'Change missing required fields: model_type, action, or data'
                            ];
                            continue; // Skip this invalid change
                        }

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
                    // Format the data as a proper sync change that pullChanges expects
                    $syncData = $this->prepareModelForHub($model);
                    $change = [
                        'model_type' => get_class($model),
                        'action' => $this->determineModelAction($model),
                        'data' => $syncData
                    ];
                    $results['data'][] = $change;
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

                // Include dependent models that should travel with the primary model
                $models = array_merge($models, $this->getDependentModelsFor($modelClass, $models));
            }
        } else {
            // Get all syncable models
            $syncableModels = $this->getSyncableModels();
            foreach ($syncableModels as $modelClass) {
                $pendingModels = $modelClass::whereIn('sync_status', ['pending', 'deleted_pending'])->get();
                // Use all() to get the actual model objects
                $models = array_merge($models, $pendingModels->all());
            }
        }

        return $models;
    }

    /**
     * Resolve and collect dependent models that should be synced with the provided primary models.
     */
    protected function getDependentModelsFor(string $primaryClass, array $primaryModels): array
    {
        if (empty($primaryModels)) {
            return [];
        }

        $dependencies = [
            // When pushing SalesOrder, also push its ItemSold rows
            'App\\Models\\SalesOrder' => [
                [
                    'class' => 'App\\Models\\ItemSold',
                    'foreign_key' => 'sales_order_id',
                ],
            ],
            // When pushing Release, also push its ReleaseDetails rows
            'App\\Models\\Release' => [
                [
                    'class' => 'App\\Models\\ReleaseDetails',
                    'foreign_key' => 'release_id',
                ],
            ],
            // When pushing ReturnItem (return header), also push its ReturnDetails rows
            'App\\Models\\ReturnItem' => [
                [
                    'class' => 'App\\Models\\ReturnDetails',
                    'foreign_key' => 'return_id',
                ],
            ],
            // When pushing StoreTransferOrder (transfer header), also push its StoreTransferItem rows
            'App\\Models\\StoreTransferOrder' => [
                [
                    'class' => 'App\\Models\\StoreTransferItem',
                    'foreign_key' => 'transfer_order_id',
                ],
            ],
            // When pushing ReceiveOrder (receive header), also push its ReceiveItem rows
            'App\\Models\\ReceiveOrder' => [
                [
                    'class' => 'App\\Models\\ReceiveItem',
                    'foreign_key' => 'receive_order_id',
                ],
            ],
        ];

        if (!isset($dependencies[$primaryClass])) {
            return [];
        }

        $dependentModels = [];

        // Collect primary IDs once
        $primaryIds = array_values(array_filter(array_map(function ($model) {
            return $model->id ?? null;
        }, $primaryModels)));

        if (empty($primaryIds)) {
            return [];
        }

        foreach ($dependencies[$primaryClass] as $dependency) {
            $dependentClass = $dependency['class'];
            $fk = $dependency['foreign_key'];

            if (class_exists($dependentClass)) {
                // Only include child rows that need syncing
                $rows = $dependentClass::whereIn($fk, $primaryIds)
                    ->whereIn('sync_status', ['pending', 'deleted_pending'])
                    ->get()
                    ->all();
                if (!empty($rows)) {
                    $dependentModels = array_merge($dependentModels, $rows);
                }
            }
        }

        return $dependentModels;
    }

    /**
     * Push model to central hub
     */
    protected function pushModelToHub($model): bool
    {
        // If this IS the central hub, mark as synced locally (no external HTTP call needed)
        if ($this->isCentralHub()) {
            Log::info('Central hub detected - marking model as synced locally', [
                'model_class' => get_class($model),
                'model_id' => $model->id
            ]);

            $model->markAsSynced();
            return true;
        }

        // Regular location: make HTTP request to external central hub
        $url = $this->centralHubUrl . '/api/sync/push';
        $payload = [
            'model_type' => get_class($model),
            'data' => $this->prepareModelForHub($model),
        ];

        Log::info('Making HTTP request to central hub', [
            'url' => $url,
            'model_type' => get_class($model),
            'model_id' => $model->id,
            'payload_size' => strlen(json_encode($payload))
        ]);

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'X-Location-ID' => $this->locationId,
            ])->post($url, $payload);

            Log::info('HTTP response received', [
                'status_code' => $response->status(),
                'response_body' => $response->body(),
                'successful' => $response->successful()
            ]);

            if ($response->successful()) {
                // Inspect hub result payload to ensure the record actually persisted
                $json = [];
                try {
                    $json = $response->json();
                } catch (\Throwable $e) {
                    // ignore JSON parse error; treat as failure below
                }

                $hubReportedFailure = false;
                if (is_array($json) && isset($json['data']) && is_array($json['data'])) {
                    $hubReportedFailure = (($json['data']['failed'] ?? 0) > 0);
                }

                if (!$hubReportedFailure) {
                    $model->markAsSynced();
                    Log::info('Model marked as synced', [
                        'model_class' => get_class($model),
                        'model_id' => $model->id
                    ]);
                    return true;
                }

                // Hub returned an application-level error even though HTTP 200
                throw new Exception('Hub reported failure: ' . $response->body());
            }

            throw new Exception('Push failed: ' . $response->body());
        } catch (Exception $e) {
            Log::error('HTTP request failed', [
                'url' => $url,
                'error' => $e->getMessage(),
                'model_class' => get_class($model),
                'model_id' => $model->id
            ]);
            throw $e;
        }
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
        // If this IS the central hub, it's always "online" (no external dependency)
        if ($this->isCentralHub()) {
            return true;
        }

        // Regular location: check connectivity to external central hub
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

        // Method 2: Check if current host matches central hub host (including localhost variations)
        $currentHost = request()->getHost();
        $hubHost = parse_url($this->centralHubUrl, PHP_URL_HOST);

        // Check exact match
        if (strtolower($currentHost) === strtolower($hubHost)) {
            return true;
        }

        // Check for localhost variations
        $localhostVariations = ['localhost', '127.0.0.1', '::1'];
        if (in_array(strtolower($currentHost), $localhostVariations)) {
            // If we're on localhost and this is configured as central hub, treat it as central hub
            if (config('sync.is_central_hub', false)) {
                return true;
            }
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
                    // Format the data as a proper sync change that pullChanges expects
                    $syncData = $this->prepareModelForHub($model);
                    $change = [
                        'model_type' => get_class($model),
                        'action' => $this->determineModelAction($model),
                        'data' => $syncData
                    ];
                    $results['data'][] = $change;
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
            'App\Models\Branch',
            'App\Models\Customer',
            'App\Models\Vendor',
            'App\Models\Store',
            'App\Models\SalesOrder',
            'App\Models\SalesReceipt',
            'App\Models\PurchaseOrder',
            'App\Models\PostInflow',
            'App\Models\PostOutflow',
            'App\Models\CashierRemittance',
            'App\Models\PriceChange',
            'App\Models\CreditTransaction',
            'App\Models\StoreItem',
            'App\Models\ItemSold',
            'App\Models\ReceiveItem',
            'App\Models\ReceiveOrder',
            'App\Models\Release',
            'App\Models\ReleaseDetails',
            'App\Models\ReturnItem',
            'App\Models\ReturnDetails',
            'App\Models\StoreTransferItem',
            'App\Models\StoreTransferOrder',
            'App\Models\User',
            'App\Models\CreateItem',
            'App\Models\TransactionJournalEntry',
            'App\Models\TransactionJournalEntryDetail',
            'App\Models\BankRemittance',
            'App\Models\CashierExpense',
            'App\Models\JournalEntryDetail',
            'App\Models\ProductAudit',
            // Add more models as needed
        ];
    }

    /**
     * Create model from incoming sync data (for central hub)
     */
    protected function createModelFromSyncData(string $modelType, array $modelData, array $syncMetadata): void
    {
        // Attach sync metadata back so fillModelFromData can handle it (and relation_sync)
        $data = $modelData;
        $data['sync_metadata'] = $syncMetadata;

        // Log the incoming data for debugging UUID primary key issues
        $primaryKey = 'id';
        if (class_exists($modelType)) {
            $tempModel = new $modelType();
            $primaryKey = method_exists($tempModel, 'getKeyName') ? $tempModel->getKeyName() : 'id';
            $usesIncrementing = method_exists($tempModel, 'getIncrementing') ? $tempModel->getIncrementing() : false;

            Log::info('Creating model from sync data', [
                'model_type' => $modelType,
                'primary_key' => $primaryKey,
                'uses_incrementing' => $usesIncrementing,
                'has_id_in_data' => isset($data[$primaryKey]),
                'id_value' => $data[$primaryKey] ?? 'not_set',
                'id_type' => isset($data[$primaryKey]) ? gettype($data[$primaryKey]) : 'n/a'
            ]);
        }

        $model = new $modelType();
        $this->fillModelFromData($model, $data);

        // Log the model state before saving
        Log::info('Model state before save', [
            'model_type' => $modelType,
            'primary_key' => $primaryKey,
            'model_id' => $model->getKey(),
            'model_id_type' => gettype($model->getKey()),
            'model_attributes' => $model->getAttributes()
        ]);

        EloquentModel::withoutEvents(function () use ($model) {
            $model->save();
        });

        // Verify the sync_status was saved correctly
        $savedModel = $modelType::find($model->id);
        if ($savedModel && $savedModel->sync_status !== 'synced') {
            Log::warning('Sync status not saved correctly, forcing update', [
                'model_type' => $modelType,
                'model_id' => $model->id,
                'expected_status' => 'synced',
                'actual_status' => $savedModel->sync_status
            ]);

            // Force update the sync_status
            $savedModel->update([
                'sync_status' => 'synced',
                'last_synced_at' => now()
            ]);
        }

        Log::info('Created model from sync data', [
            'model_type' => $modelType,
            'sync_id' => $model->sync_id,
            'location_id' => $model->location_id,
            'sync_status' => $savedModel ? $savedModel->sync_status : 'unknown'
        ]);
    }

    /**
     * Update model from incoming sync data (for central hub)
     */
    protected function updateModelFromSyncData(string $modelType, array $modelData, array $syncMetadata): void
    {
        $syncId = $syncMetadata['sync_id'] ?? null;

        if (!$syncId) {
            throw new Exception('Sync ID required for updates');
        }

        $model = $modelType::where('sync_id', $syncId)->first();

        if (!$model) {
            // If model doesn't exist, create it
            $this->createModelFromSyncData($modelType, $modelData, $syncMetadata);
            return;
        }

        // Attach sync metadata back so fillModelFromData can handle it (and relation_sync)
        $data = $modelData;
        $data['sync_metadata'] = $syncMetadata;

        // Use shared filler (handles relation_sync FK remapping)
        $this->fillModelFromData($model, $data);
        EloquentModel::withoutEvents(function () use ($model) {
            $model->save();
        });

        // Verify the sync_status was saved correctly
        $savedModel = $modelType::find($model->id);
        if ($savedModel && $savedModel->sync_status !== 'synced') {
            Log::warning('Sync status not saved correctly during update, forcing update', [
                'model_type' => $modelType,
                'model_id' => $model->id,
                'expected_status' => 'synced',
                'actual_status' => $savedModel->sync_status
            ]);

            // Force update the sync_status
            $savedModel->update([
                'sync_status' => 'synced',
                'last_synced_at' => now()
            ]);
        }

        Log::info('Updated model from sync data', [
            'model_type' => $modelType,
            'sync_id' => $model->sync_id,
            'location_id' => $model->location_id,
            'sync_status' => $savedModel ? $savedModel->sync_status : 'unknown'
        ]);
    }

    /**
     * Delete model from incoming sync data (for central hub)
     */
    protected function deleteModelFromSyncData(string $modelType, array $syncMetadata): void
    {
        $syncId = $syncMetadata['sync_id'] ?? null;

        if (!$syncId) {
            throw new Exception('Sync ID required for deletions');
        }

        $model = $modelType::where('sync_id', $syncId)->first();

        if ($model) {
            $model->delete();

            Log::info('Deleted model from sync data', [
                'model_type' => $modelType,
                'sync_id' => $syncId
            ]);
        }
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
        EloquentModel::withoutEvents(function () use ($model) {
            $model->save();
        });
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
        // Get all fillable attributes from the model
        $syncData = $model->toArray();

        // Ensure model has a sync_id; generate and persist if missing so children can reference it
        if (empty($model->sync_id)) {
            try {
                $model->sync_id = Str::uuid();
                // Avoid triggering events that might alter business state
                $model->save();
            } catch (\Throwable $e) {
                // If saving fails, we still include a generated sync_id in payload for linkage
                $model->sync_id = $model->sync_id ?? (string) Str::uuid();
            }
        }

        // Handle primary key: remove for incrementing models, ensure it exists for non-incrementing (UUID) models
        try {
            $primaryKey = method_exists($model, 'getKeyName') ? $model->getKeyName() : 'id';
            $usesIncrementing = method_exists($model, 'getIncrementing') ? $model->getIncrementing() : false;

            if ($primaryKey) {
                if ($usesIncrementing) {
                    // For auto-incrementing models, remove the primary key to let the hub generate it
                    unset($syncData[$primaryKey]);
                } else {
                    // For UUID/non-incrementing models, ensure we have a valid primary key
                    $primaryValue = $model->getKey();
                    if (empty($primaryValue)) {
                        // Generate UUID if missing
                        $primaryValue = (string) Str::uuid();
                        $model->setAttribute($primaryKey, $primaryValue);
                        try {
                            EloquentModel::withoutEvents(function () use ($model) {
                                $model->save();
                            });
                        } catch (\Throwable $e) {
                            // ignore inability to persist primary key; still send generated value
                        }
                    }
                    // Always set the primary key in syncData for non-incrementing models
                    $syncData[$primaryKey] = $primaryValue;
                }
            }
        } catch (\Throwable $e) {
            // ignore key handling issues
            Log::warning('Error handling primary key in sync preparation', [
                'model' => get_class($model),
                'error' => $e->getMessage()
            ]);
        }

        // Remove sync-related fields that will be handled separately
        unset($syncData['sync_id']);
        unset($syncData['location_id']);
        unset($syncData['sync_status']);
        unset($syncData['sync_version']);
        unset($syncData['last_synced_at']);
        unset($syncData['last_sync_attempt_at']);
        unset($syncData['sync_error']);

        // Remove user foreign keys from payload to avoid FK issues on hub; hub may not have same users
        unset($syncData['created_by']);
        unset($syncData['modified_by']);
        unset($syncData['deleted_by']);
        unset($syncData['approved_by']);

        // Ensure location_id is always set to a valid value
        $locationId = $model->location_id ?? config('app.location_id', 'unknown');

        // Add sync metadata in the structure the central hub expects
        $syncData['sync_metadata'] = [
            'sync_id' => $model->sync_id,
            'location_id' => $locationId,
            'sync_version' => $model->sync_version ?? 1,
            'sync_status' => $model->sync_status,
            'action' => $this->determineModelAction($model),
            'created_at' => $this->formatIso($model->created_at ?? null),
            'updated_at' => $this->formatIso($model->updated_at ?? null),
            'last_synced_at' => $this->formatIso($model->last_synced_at ?? null),
            'last_sync_attempt_at' => $this->formatIso($model->last_sync_attempt_at ?? null),
        ];

        // Include relation sync mapping for known child models so the hub can remap FKs by sync_id
        $relationSync = $this->buildRelationSyncMapping($model);
        if (!empty($relationSync)) {
            $syncData['relation_sync'] = $relationSync;
        }

        // Add soft delete information if applicable
        if (method_exists($model, 'trashed') && $model->trashed()) {
            $syncData['sync_metadata']['action'] = 'delete';
            $syncData['sync_metadata']['deleted_at'] = $this->formatIso($model->deleted_at ?? null);
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
    // protected function fillModelFromData($model, array $data): void
    // {
    //     $syncMetadata = $data['sync_metadata'] ?? [];
    //     unset($data['sync_metadata']);
    //     $relationSync = $data['relation_sync'] ?? [];
    //     unset($data['relation_sync']);

    //     // Handle primary key for UUID/non-incrementing models BEFORE other operations
    //     // This ensures the primary key is set even if it's not in the fillable array
    //     try {
    //         $primaryKey = method_exists($model, 'getKeyName') ? $model->getKeyName() : 'id';
    //         $usesIncrementing = method_exists($model, 'getIncrementing') ? $model->getIncrementing() : false;

    //         if ($primaryKey && !$usesIncrementing) {
    //             // For UUID/non-incrementing models, handle primary key
    //             if (isset($data[$primaryKey]) && !empty($data[$primaryKey]) && $data[$primaryKey] !== '') {
    //                 // Set the primary key from data if it's provided and not empty
    //                 $model->setAttribute($primaryKey, $data[$primaryKey]);
    //             }
    //             // Always remove from data array to prevent it from being processed as a regular field
    //             // The HasUuid trait will generate a UUID if the key is still empty during model creation
    //             unset($data[$primaryKey]);
    //         }
    //     } catch (\Throwable $e) {
    //         // Log but continue - primary key handling is not critical if it fails
    //         Log::warning('Error handling primary key in fillModelFromData', [
    //             'model' => get_class($model),
    //             'error' => $e->getMessage()
    //         ]);
    //     }

    //     // Before filling, remap any foreign keys using provided relation sync info
    //     if (is_array($relationSync) && !empty($relationSync)) {
    //         foreach ($relationSync as $foreignKey => $relationInfo) {
    //             $relatedType = $relationInfo['model_type'] ?? null;
    //             $relatedSyncId = $relationInfo['sync_id'] ?? null;
    //             if ($relatedType && $relatedSyncId) {
    //                 try {
    //                     $related = $relatedType::where('sync_id', $relatedSyncId)->first();
    //                     if ($related && $model->isFillable($foreignKey)) {
    //                         $data[$foreignKey] = $related->id;
    //                     }
    //                 } catch (\Throwable $e) {
    //                     // ignore resolution errors; fall back to provided value
    //                 }
    //             }
    //         }
    //     }

    //     // Guard user FKs that may not exist on hub; null them to avoid FK violations
    //     try {
    //         if (class_exists('App\\Models\\User')) {
    //             foreach (['created_by','modified_by','deleted_by','approved_by'] as $userFk) {
    //                 if (array_key_exists($userFk, $data) && !empty($data[$userFk])) {
    //                     $userExists = \App\Models\User::where('id', $data[$userFk])->exists();
    //                     if (!$userExists) {
    //                         $data[$userFk] = null;
    //                     }
    //                 }
    //             }
    //         }
    //     } catch (\Throwable $e) {
    //         // ignore user FK validation errors; proceed with best effort
    //     }

    //     // Set default values for null fields that might be required on hub to prevent NOT NULL violations
    //     // Use empty string for string/text fields, 0 for numeric, false for boolean
    //     // BUT exclude primary key from this treatment
    //     $primaryKey = method_exists($model, 'getKeyName') ? $model->getKeyName() : 'id';
    //     foreach ($data as $key => $value) {
    //         // Skip primary key and don't set empty strings for it
    //         if ($key === $primaryKey) {
    //             continue;
    //         }
    //         if ($value === null && $model->isFillable($key)) {
    //             // Set empty string as default for nullable string fields that might be required on hub
    //             $data[$key] = '';
    //         }
    //     }

    //     foreach ($data as $key => $value) {
    //         // Skip primary key if already handled above
    //         if ($key === $primaryKey) {
    //             continue;
    //         }
    //         if ($model->isFillable($key)) {
    //             $model->$key = $value;
    //         }
    //     }

    //     // Preserve sync metadata
    //     $model->sync_id = $syncMetadata['sync_id'] ?? $model->sync_id;
    //     $model->location_id = $syncMetadata['location_id'] ?? $model->location_id;
    //     $model->sync_version = $syncMetadata['sync_version'] ?? $model->sync_version;
    //     $model->sync_status = 'synced';
    //     $model->last_synced_at = now();
    // }

    /**
     * Fill model with data from remote
     */
    protected function fillModelFromData($model, array $data): void
    {
        $syncMetadata = $data['sync_metadata'] ?? [];
        unset($data['sync_metadata']);
        $relationSync = $data['relation_sync'] ?? [];
        unset($data['relation_sync']);

        // Handle primary key for UUID/non-incrementing models BEFORE other operations
        try {
            $primaryKey = method_exists($model, 'getKeyName') ? $model->getKeyName() : 'id';
            $usesIncrementing = method_exists($model, 'getIncrementing') ? $model->getIncrementing() : false;

            if ($primaryKey && !$usesIncrementing) {
                // For UUID/non-incrementing models, handle primary key
                if (isset($data[$primaryKey]) && !empty($data[$primaryKey]) && $data[$primaryKey] !== '') {
                    // Set the primary key from data if it's provided and not empty
                    $model->setAttribute($primaryKey, $data[$primaryKey]);
                    Log::info('Set primary key from data', [
                        'model' => get_class($model),
                        'primary_key' => $primaryKey,
                        'value' => $data[$primaryKey]
                    ]);
                } elseif (!$model->exists) {
                    // Model doesn't exist yet and no valid primary key provided - generate one
                    $newId = (string) Str::uuid();
                    $model->setAttribute($primaryKey, $newId);
                    Log::info('Generated new UUID for model', [
                        'model' => get_class($model),
                        'primary_key' => $primaryKey,
                        'value' => $newId
                    ]);
                }
                // Remove from data array to prevent it from being processed as a regular field
                unset($data[$primaryKey]);
            } else {
                // For incrementing models, always remove the primary key
                unset($data[$primaryKey]);
            }
        } catch (\Throwable $e) {
            Log::warning('Error handling primary key in fillModelFromData', [
                'model' => get_class($model),
                'error' => $e->getMessage()
            ]);
        }

        // Before filling, remap any foreign keys using provided relation sync info
        if (is_array($relationSync) && !empty($relationSync)) {
            foreach ($relationSync as $foreignKey => $relationInfo) {
                $relatedType = $relationInfo['model_type'] ?? null;
                $relatedSyncId = $relationInfo['sync_id'] ?? null;
                if ($relatedType && $relatedSyncId) {
                    try {
                        $related = $relatedType::where('sync_id', $relatedSyncId)->first();
                        if ($related && $model->isFillable($foreignKey)) {
                            $data[$foreignKey] = $related->id;
                        }
                    } catch (\Throwable $e) {
                        // ignore resolution errors; fall back to provided value
                    }
                }
            }
        }

        // Guard user FKs that may not exist on hub; null them to avoid FK violations
        try {
            if (class_exists('App\\Models\\User')) {
                foreach (['created_by', 'modified_by', 'deleted_by', 'approved_by'] as $userFk) {
                    if (array_key_exists($userFk, $data) && !empty($data[$userFk])) {
                        $userExists = \App\Models\User::where('id', $data[$userFk])->exists();
                        if (!$userExists) {
                            $data[$userFk] = null;
                        }
                    }
                }
            }
        } catch (\Throwable $e) {
            // ignore user FK validation errors; proceed with best effort
        }

        // Set default values for null fields that might be required on hub
        foreach ($data as $key => $value) {
            if ($value === null && $model->isFillable($key)) {
                // Set empty string as default for nullable string fields
                $data[$key] = '';
            }
        }

        // Fill all remaining fillable attributes
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

        // Final validation: ensure UUID models have a valid primary key before save
        try {
            $primaryKey = method_exists($model, 'getKeyName') ? $model->getKeyName() : 'id';
            $usesIncrementing = method_exists($model, 'getIncrementing') ? $model->getIncrementing() : false;

            if ($primaryKey && !$usesIncrementing && !$model->exists) {
                $currentKey = $model->getAttribute($primaryKey);
                if (empty($currentKey)) {
                    $newId = (string) Str::uuid();
                    $model->setAttribute($primaryKey, $newId);
                    Log::warning('Primary key was empty before save, generated UUID', [
                        'model' => get_class($model),
                        'primary_key' => $primaryKey,
                        'generated_value' => $newId
                    ]);
                }
            }
        } catch (\Throwable $e) {
            Log::error('Error in final primary key validation', [
                'model' => get_class($model),
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Build relation-sync mapping for known child models to enable FK remapping on hub
     */
    protected function buildRelationSyncMapping($model): array
    {
        $mapping = [];
        $modelClass = get_class($model);

        try {
            switch ($modelClass) {
                case 'App\\Models\\ReturnDetails':
                    if (!empty($model->return_id)) {
                        $parent = \App\Models\ReturnItem::find($model->return_id);
                        if ($parent && !empty($parent->sync_id)) {
                            $mapping['return_id'] = [
                                'model_type' => 'App\\Models\\ReturnItem',
                                'sync_id' => $parent->sync_id,
                            ];
                        }
                    }
                    break;
                case 'App\\Models\\ReleaseDetails':
                    if (!empty($model->release_id)) {
                        $parent = \App\Models\Release::find($model->release_id);
                        if ($parent && !empty($parent->sync_id)) {
                            $mapping['release_id'] = [
                                'model_type' => 'App\\Models\\Release',
                                'sync_id' => $parent->sync_id,
                            ];
                        }
                    }
                    break;
                case 'App\\Models\\StoreTransferItem':
                    if (!empty($model->transfer_order_id)) {
                        $parent = \App\Models\StoreTransferOrder::find($model->transfer_order_id);
                        if ($parent && !empty($parent->sync_id)) {
                            $mapping['transfer_order_id'] = [
                                'model_type' => 'App\\Models\\StoreTransferOrder',
                                'sync_id' => $parent->sync_id,
                            ];
                        }
                    }
                    break;
                case 'App\\Models\\ReceiveItem':
                    if (!empty($model->receive_order_id)) {
                        $parent = \App\Models\ReceiveOrder::find($model->receive_order_id);
                        if ($parent && !empty($parent->sync_id)) {
                            $mapping['receive_order_id'] = [
                                'model_type' => 'App\\Models\\ReceiveOrder',
                                'sync_id' => $parent->sync_id,
                            ];
                        }
                    }
                    break;
                default:
                    // no-op
                    break;
            }
        } catch (\Throwable $e) {
            // ignore mapping errors
        }

        return $mapping;
    }

    /**
     * Safely format a datetime (Carbon|string|null) to ISO8601 string
     */
    protected function formatIso($value): ?string
    {
        if ($value === null) {
            return null;
        }

        try {
            if ($value instanceof \Carbon\CarbonInterface) {
                return $value->toISOString();
            }
            // Attempt to parse string or DateTime
            return \Carbon\Carbon::parse($value)->toISOString();
        } catch (\Throwable $e) {
            // Fallback: best-effort string cast
            return (string) $value;
        }
    }
}
