# Multi-Location Data Synchronization System

This document describes the comprehensive data synchronization solution implemented for your Laravel application to handle multi-location deployments with offline capabilities.

## Overview

The synchronization system provides:
- **Automatic synchronization** of data changes across locations
- **Offline support** with queuing and retry mechanisms
- **Conflict resolution** based on timestamps and business rules
- **Easy integration** with existing models using a trait
- **Central hub coordination** for reliable data distribution
- **Real-time status monitoring** and health checks

## Architecture

```
┌─────────────────┐    ┌─────────────────┐    ┌─────────────────┐
│   Location A    │    │   Location B    │    │   Location C    │
│                 │    │                 │    │                 │
│ ┌─────────────┐ │    │ ┌─────────────┐ │    │ ┌─────────────┐ │
│ │Local Database│ │    │ │Local Database│ │    │ │Local Database│ │
│ └─────────────┘ │    │ └─────────────┘ │    │ └─────────────┘ │
│ ┌─────────────┐ │    │ ┌─────────────┐ │    │ ┌─────────────┐ │
│ │Sync Queue   │ │    │ │Sync Queue   │ │    │ │Sync Queue   │ │
│ └─────────────┘ │    │ └─────────────┘ │    │ └─────────────┘ │
└─────────────────┘    └─────────────────┘    └─────────────────┘
         │                       │                       │
         └───────────────────────┼───────────────────────┘
                                 │
                    ┌─────────────────┐
                    │  Central Hub    │
                    │                 │
                    │ ┌─────────────┐ │
                    │ │Sync Service │ │
                    │ └─────────────┘ │
                    │ ┌─────────────┐ │
                    │ │Conflict     │ │
                    │ │Resolution   │ │
                    │ └─────────────┘ │
                    └─────────────────┘
```

## Features

### 1. Syncable Trait
- Automatically adds synchronization capabilities to any model
- Handles sync ID generation, versioning, and status tracking
- Minimal developer effort required

### 2. Offline Queue System
- Stores changes when offline
- Automatic retry with exponential backoff
- Priority-based processing for critical data

### 3. Conflict Resolution
- Timestamp-based conflict detection
- Configurable resolution strategies
- Location priority support

### 4. Central Hub Integration
- RESTful API for data exchange
- Authentication and authorization
- Health monitoring and status reporting

## Installation & Setup

### 1. Environment Configuration

Add these variables to your `.env` file:

```env
# Location identification
APP_LOCATION_ID=store_001

# Central hub configuration
SYNC_CENTRAL_HUB_URL=https://sync.yourcompany.com
SYNC_API_KEY=your_api_key_here

# Sync settings
SYNC_BATCH_SIZE=50
SYNC_MAX_RETRIES=3
SYNC_RETRY_DELAY=60
```

### 2. Database Migrations

Run the migrations to create sync tables and add sync columns:

```bash
php artisan migrate
```

This will:
- Create the `sync_queues` table
- Add sync columns to existing tables (journal_entries, transactions, etc.)

### 3. Configuration

The sync configuration is in `config/sync.php`. Key settings:

```php
'models' => [
    'App\Models\JournalEntry' => [
        'priority' => 10,  // Higher priority = sync first
        'sync_fields' => ['*'],  // All fields
        'exclude_fields' => [],  // Fields to exclude
    ],
    // Add more models as needed
],

'conflict_resolution' => [
    'strategy' => 'timestamp',  // timestamp, manual, location_priority
    'location_priorities' => [
        'headquarters' => 100,
        'main_warehouse' => 90,
        'branch_1' => 80,
    ],
],
```

## Making Models Syncable

### Option 1: Using the Trait (Recommended)

Simply add the `Syncable` trait to any model:

```php
<?php

namespace App\Models;

use App\Models\Concerns\Syncable;
use Illuminate\Database\Eloquent\Model;

class JournalEntry extends Model
{
    use Syncable;
    
    // Your existing model code...
}
```

### Option 2: Manual Implementation

If you need custom sync behavior, implement the sync methods manually:

```php
class CustomModel extends Model
{
    public function needsSync(): bool
    {
        return $this->sync_status === 'pending';
    }
    
    public function markAsSynced(): void
    {
        $this->update(['sync_status' => 'synced']);
    }
    
    public function getSyncData(): array
    {
        return $this->toArray();
    }
}
```

## Usage

### 1. Command Line Interface

#### Manual Synchronization

```bash
# Push local changes to central hub
php artisan sync:data --push

# Pull changes from central hub
php artisan sync:data --pull

# Process sync queue (for offline scenarios)
php artisan sync:data --queue

# Sync specific model
php artisan sync:data --push --model="App\Models\JournalEntry"

# Full sync (push + pull)
php artisan sync:data
```

#### Scheduled Synchronization

Add to your `app/Console/Kernel.php`:

```php
protected function schedule(Schedule $schedule)
{
    // Sync every 5 minutes
    $schedule->command('sync:data --push')->everyFiveMinutes();
    $schedule->command('sync:data --pull')->everyFiveMinutes();
    
    // Process queue every minute
    $schedule->command('sync:data --queue')->everyMinute();
}
```

### 2. API Endpoints

All endpoints require authentication:

```bash
# Push local changes
POST /api/sync/push
{
    "model_type": "App\\Models\\JournalEntry",
    "force": false
}

# Pull changes from hub
POST /api/sync/pull

# Process sync queue
POST /api/sync/queue/process

# Get sync status
GET /api/sync/status

# Force sync specific model
POST /api/sync/force
{
    "model_type": "App\\Models\\JournalEntry",
    "model_id": 123
}
```

### 3. Programmatic Usage

```php
use App\Services\SyncService;

class SomeController extends Controller
{
    public function someAction(SyncService $syncService)
    {
        // Push changes immediately
        $results = $syncService->pushChanges();
        
        // Pull changes
        $results = $syncService->pullChanges();
        
        // Process queue
        $results = $syncService->processQueue();
    }
}
```

## Conflict Resolution

### Automatic Resolution

The system automatically resolves conflicts using these strategies:

1. **Timestamp-based** (default): Newer version wins
2. **Location priority**: Higher priority location wins
3. **Manual**: Requires human intervention

### Custom Conflict Resolution

Override the conflict resolution in your models:

```php
class JournalEntry extends Model
{
    use Syncable;
    
    protected function resolveConflict($localModel, array $remoteData): bool
    {
        // Custom business logic
        if ($this->isApproved() && !$remoteData['is_approved']) {
            return false; // Keep local version
        }
        
        // Default behavior
        return parent::resolveConflict($localModel, $remoteData);
    }
}
```

## Monitoring & Troubleshooting

### 1. Status Monitoring

```bash
# Check sync status
GET /api/sync/status

# Response includes:
{
    "location_id": "store_001",
    "last_sync": {
        "last_sync_at": "2024-01-15T10:30:00Z",
        "models_pending": 5,
        "models_failed": 0
    },
    "queue_status": {
        "pending": 3,
        "processing": 1,
        "failed": 0,
        "completed": 150
    },
    "online_status": true
}
```

### 2. Logging

Sync activities are logged to the `sync` channel:

```php
// In config/logging.php
'channels' => [
    'sync' => [
        'driver' => 'daily',
        'path' => storage_path('logs/sync.log'),
        'level' => env('SYNC_LOG_LEVEL', 'info'),
        'days' => 14,
    ],
],
```

### 3. Common Issues

#### Sync Failures

```bash
# Check failed items
php artisan tinker
>>> App\Models\SyncQueue::failed()->get()

# Reset failed items for retry
>>> App\Models\SyncQueue::failed()->update(['status' => 'pending'])
```

#### Offline Scenarios

```bash
# Check queue status
php artisan sync:data --queue

# Force online sync
php artisan sync:data --push --force
```

## Performance Optimization

### 1. Batch Processing

```php
// Process in smaller batches
'sync' => [
    'batch_size' => 25,  // Default: 50
],
```

### 2. Selective Synchronization

```php
// Only sync specific fields
'models' => [
    'App\Models\JournalEntry' => [
        'sync_fields' => ['description', 'amount', 'date'],
        'exclude_fields' => ['created_by', 'updated_at'],
    ],
],
```

### 3. Database Indexing

The migrations automatically create indexes for:
- `sync_status` + `last_synced_at`
- `location_id`
- `sync_id`

## Security Considerations

### 1. API Authentication

All sync endpoints require authentication via Laravel Sanctum.

### 2. Data Validation

```php
// Validate sync data before processing
protected function validateSyncData(array $data): bool
{
    $rules = [
        'sync_id' => 'required|uuid',
        'location_id' => 'required|string',
        'sync_version' => 'required|integer|min:1',
    ];
    
    return Validator::make($data, $rules)->passes();
}
```

### 3. Rate Limiting

Add rate limiting to sync endpoints:

```php
// In routes/api.php
Route::middleware(['auth:sanctum', 'throttle:60,1'])->group(function () {
    Route::prefix('sync')->group(function () {
        // Sync routes...
    });
});
```

## Testing

### 1. Unit Tests

```php
class SyncServiceTest extends TestCase
{
    public function test_push_changes()
    {
        $service = new SyncService();
        $results = $service->pushChanges();
        
        $this->assertGreaterThan(0, $results['success']);
    }
}
```

### 2. Integration Tests

```php
class SyncControllerTest extends TestCase
{
    public function test_sync_status_endpoint()
    {
        $response = $this->getJson('/api/sync/status');
        $response->assertStatus(200);
        $response->assertJsonStructure(['location_id', 'last_sync']);
    }
}
```

## Deployment

### 1. Production Setup

```bash
# Set production environment
APP_ENV=production
APP_DEBUG=false

# Configure central hub
SYNC_CENTRAL_HUB_URL=https://sync.production.com
SYNC_API_KEY=prod_api_key_here

# Optimize for production
php artisan config:cache
php artisan route:cache
```

### 2. Monitoring

```bash
# Set up monitoring for sync jobs
# Add to your monitoring system:
# - Queue size monitoring
# - Sync failure alerts
# - Performance metrics
```

### 3. Backup Strategy

```bash
# Backup sync queue before major updates
php artisan tinker
>>> DB::table('sync_queues')->get()->toArray();
```

## Support & Maintenance

### 1. Regular Maintenance

```bash
# Clean old sync logs
php artisan sync:clean-logs

# Reset stuck sync items
php artisan sync:reset-stuck

# Validate sync integrity
php artisan sync:validate
```

### 2. Troubleshooting Commands

```bash
# Check sync health
php artisan sync:health

# Repair sync data
php artisan sync:repair

# Generate sync report
php artisan sync:report
```

## Conclusion

This synchronization system provides a robust, scalable solution for multi-location data consistency. It handles offline scenarios gracefully, provides conflict resolution, and requires minimal developer effort to implement.

Key benefits:
- **Reliable**: Handles network failures and offline scenarios
- **Scalable**: Supports unlimited locations and models
- **Maintainable**: Clean separation of concerns and easy debugging
- **Flexible**: Configurable conflict resolution and field mapping
- **Secure**: Authentication, validation, and rate limiting

For questions or support, refer to the logging system and monitoring endpoints to diagnose any issues. 