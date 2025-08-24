<?php

namespace App\Http\Controllers;

use App\Services\SyncService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class SyncController extends Controller
{
    protected $syncService;

    public function __construct(SyncService $syncService)
    {
        $this->syncService = $syncService;
    }

    /**
     * Push local changes to central hub
     */
    public function push(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'model_type' => 'nullable|string',
                'force' => 'boolean'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            $modelType = $request->input('model_type');
            $results = $this->syncService->pushChanges($modelType);

            return response()->json([
                'success' => true,
                'message' => 'Changes pushed successfully',
                'data' => $results
            ]);

        } catch (\Exception $e) {
            Log::error('Sync push failed', ['error' => $e->getMessage()]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to push changes',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Pull changes from central hub
     */
    public function pull(Request $request): JsonResponse
    {
        try {
            $results = $this->syncService->pullChanges();

            return response()->json([
                'success' => true,
                'message' => 'Changes pulled successfully',
                'data' => $results
            ]);

        } catch (\Exception $e) {
            Log::error('Sync pull failed', ['error' => $e->getMessage()]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to pull changes',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Pull changes for central hub (incoming pull requests)
     * This endpoint is called by the central hub to retrieve local changes
     * Central hub then distributes these changes to other locations
     */
    public function pullForHub(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'model_type' => 'nullable|string',
                'limit' => 'integer|min:1|max:1000'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            $modelType = $request->input('model_type');
            $limit = $request->input('limit', 100);
            
            $results = $this->syncService->pullChangesForHub($modelType, $limit);

            return response()->json($results);

        } catch (\Exception $e) {
            Log::error('Central hub pull request failed', [
                'error' => $e->getMessage(),
                'request' => $request->all()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to process central hub pull request',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Process sync queue
     */
    public function processQueue(Request $request): JsonResponse
    {
        try {
            $results = $this->syncService->processQueue();

            return response()->json([
                'success' => true,
                'message' => 'Queue processed successfully',
                'data' => $results
            ]);

        } catch (\Exception $e) {
            Log::error('Queue processing failed', ['error' => $e->getMessage()]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to process queue',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get sync status
     */
    public function status(): JsonResponse
    {
        try {
            $status = [
                'location_id' => config('app.location_id'),
                'last_sync' => $this->getLastSyncInfo(),
                'queue_status' => $this->getQueueStatus(),
                'online_status' => $this->checkOnlineStatus()
            ];

            return response()->json([
                'success' => true,
                'data' => $status
            ]);

        } catch (\Exception $e) {
            Log::error('Status check failed', ['error' => $e->getMessage()]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to get status',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get last synchronization information
     */
    protected function getLastSyncInfo(): array
    {
        // Get the most recent sync from any model
        $lastSync = DB::table('journal_entries')
            ->select('last_synced_at')
            ->whereNotNull('last_synced_at')
            ->orderBy('last_synced_at', 'desc')
            ->first();

        return [
            'last_sync_at' => $lastSync?->last_synced_at,
            'models_pending' => $this->getPendingModelsCount(),
            'models_failed' => $this->getFailedModelsCount()
        ];
    }

    /**
     * Get queue status
     */
    protected function getQueueStatus(): array
    {
        $queue = \App\Models\SyncQueue::query();
        
        return [
            'pending' => $queue->where('status', 'pending')->count(),
            'processing' => $queue->where('status', 'processing')->count(),
            'failed' => $queue->where('status', 'failed')->count(),
            'completed' => $queue->where('status', 'completed')->count(),
        ];
    }

    /**
     * Check online status
     */
    protected function checkOnlineStatus(): bool
    {
        try {
            $response = \Illuminate\Support\Facades\Http::timeout(5)
                ->get(config('sync.central_hub_url') . '/health');
            return $response->successful();
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Get count of models pending sync
     */
    protected function getPendingModelsCount(): int
    {
        $count = 0;
        $syncableModels = config('sync.models', []);
        
        foreach (array_keys($syncableModels) as $modelClass) {
            if (class_exists($modelClass) && method_exists($modelClass, 'scopeBySyncStatus')) {
                $count += $modelClass::whereIn('sync_status', ['pending', 'deleted_pending'])->count();
            }
        }
        
        return $count;
    }

    /**
     * Get count of models with failed sync
     */
    protected function getFailedModelsCount(): int
    {
        $count = 0;
        $syncableModels = config('sync.models', []);
        
        foreach (array_keys($syncableModels) as $modelClass) {
            if (class_exists($modelClass) && method_exists($modelClass, 'scopeBySyncStatus')) {
                $count += $modelClass::bySyncStatus('failed')->count();
            }
        }
        
        return $count;
    }

    /**
     * Force synchronization for specific model
     */
    public function forceSync(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'model_type' => 'required|string',
                'model_id' => 'required|integer'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            $modelType = $request->input('model_type');
            $modelId = $request->input('model_id');

            if (!class_exists($modelType)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid model type'
                ], 422);
            }

            $model = $modelType::find($modelId);
            if (!$model) {
                return response()->json([
                    'success' => false,
                    'message' => 'Model not found'
                ], 404);
            }

            // Force sync status to pending
            $model->update(['sync_status' => 'pending']);
            
            // Add to queue
            $this->syncService->addToQueue($model, 'update');

            return response()->json([
                'success' => true,
                'message' => 'Model queued for synchronization'
            ]);

        } catch (\Exception $e) {
            Log::error('Force sync failed', ['error' => $e->getMessage()]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to force sync',
                'error' => $e->getMessage()
            ], 500);
        }
    }
} 