# Sales and Inventory Software - Sync System

## Overview

This sync system provides automated data synchronization between local and remote systems for the Sales and Inventory Software. It includes intelligent retry mechanisms, health checks, batch processing, and comprehensive monitoring.

## Features

- 🔄 **Multiple Sync Modes**: Full, push-only, pull-only, and queue-only synchronization
- 🚀 **Batch Processing**: Configurable batch sizes for optimal performance
- 💾 **Memory Management**: Memory usage tracking and optimization
- 🔁 **Retry Mechanism**: Automatic retry of failed sync operations
- 🏥 **Health Checks**: System health monitoring before sync operations
- 📊 **Progress Tracking**: Real-time progress updates and detailed reporting
- 📈 **Performance Monitoring**: Comprehensive metrics and statistics
- 🔒 **Security**: Encrypted data transmission and validation
- 📝 **Logging**: Detailed logging for audit trails and debugging

## Installation

### 1. Register Service Provider

Add the `SyncServiceProvider` to your `config/app.php`:

```php
'providers' => [
    // ... other providers
    App\Providers\SyncServiceProvider::class,
],
```

### 2. Publish Configuration

```bash
php artisan vendor:publish --tag=sync-config
```

### 3. Environment Variables

Add these variables to your `.env` file:

```bash
# Sync Configuration
SYNC_BATCH_SIZE=100
SYNC_MAX_MEMORY_MB=512
SYNC_RETRY_ATTEMPTS=3
SYNC_TIMEOUT_SECONDS=300
SYNC_HEALTH_CHECK_ENABLED=true
SYNC_MONITORING_ENABLED=true

# Queue Configuration
SYNC_QUEUE_CONNECTION=database
SYNC_QUEUE_NAME=sync

# Notifications
SYNC_NOTIFICATIONS_ENABLED=false
SYNC_NOTIFICATION_CHANNELS=mail
```

### 4. Database Migration

Ensure you have the necessary database tables for sync operations:

```bash
php artisan migrate
```

## Usage

### Basic Commands

#### Full Synchronization
```bash
php artisan sync:auto
```

#### Push-Only Sync
```bash
php artisan sync:auto --mode=push-only
```

#### Pull-Only Sync
```bash
php artisan sync:auto --mode=pull-only
```

#### Queue Processing Only
```bash
php artisan sync:auto --mode=queue-only
```

### Advanced Options

#### Health Check Before Sync
```bash
php artisan sync:auto --health-check
```

#### Retry Failed Items
```bash
php artisan sync:auto --retry-failed
```

#### Force Sync (Ignore Offline Status)
```bash
php artisan sync:auto --force
```

#### Detailed Output
```bash
php artisan sync:auto --detailed
```

#### Complete Example
```bash
php artisan sync:auto \
    --mode=full \
    --retry-failed \
    --health-check \
    --force \
    --detailed
```

## Configuration

### Sync Models

Configure which models should participate in synchronization in `config/sync.php`:

```php
'models' => [
    'App\Models\Product' => [
        'batch_size' => 50,
        'sync_fields' => ['name', 'price', 'stock'],
        'exclude_fields' => ['created_at', 'updated_at'],
    ],
    'App\Models\Customer' => [
        'batch_size' => 100,
        'sync_fields' => ['*'],
        'exclude_fields' => ['password'],
    ],
],
```

### Batch Processing

```php
'batch_size' => env('SYNC_BATCH_SIZE', 100),
'max_memory_mb' => env('SYNC_MAX_MEMORY_MB', 512),
```

### Retry Configuration

```php
'retry_attempts' => env('SYNC_RETRY_ATTEMPTS', 3),
'retry_delay_seconds' => env('SYNC_RETRY_DELAY_SECONDS', 60),
```

## Scheduling

### Automated Sync

Enable automated synchronization by setting in your `.env`:

```bash
SYNC_SCHEDULING_ENABLED=true
SYNC_SCHEDULING_INTERVAL_MINUTES=15
SYNC_SCHEDULING_DEFAULT_MODE=full
```

### Manual Crontab

Add to your server's crontab:

```bash
# Sync every 15 minutes
*/15 * * * * cd /path/to/project && php artisan sync:auto --mode=full

# Health check every hour
0 * * * * cd /path/to/project && php artisan sync:auto --health-check --mode=queue-only
```

## Testing

### Run Tests

```bash
# Run all sync tests
php artisan test --filter=AutoSyncTest

# Run specific test
php artisan test tests/Feature/Console/AutoSyncTest.php
```

### Test Coverage

The test suite covers:
- All sync modes (full, push, pull, queue)
- Error handling and recovery
- Health checks
- Retry mechanisms
- Memory management
- Progress tracking

## Monitoring

### Logs

Sync operations are logged to:
- `storage/logs/laravel.log` - General sync logs
- `storage/logs/sync-scheduled.log` - Scheduled sync logs

### Statistics

Sync statistics are cached and can be accessed via:

```php
// Get latest sync stats
$stats = Cache::get('sync_stats_' . now()->format('Y-m-d_H-i-s'));

// Get all sync stats for today
$todayStats = Cache::get('sync_stats_' . now()->format('Y-m-d'));
```

### Performance Metrics

Monitor key metrics:
- Sync duration
- Success/failure rates
- Memory usage
- Queue processing times

## Troubleshooting

### Common Issues

#### 1. Memory Exceeded
```bash
# Reduce batch size
SYNC_BATCH_SIZE=50

# Increase memory limit
SYNC_MAX_MEMORY_MB=1024
```

#### 2. Network Timeouts
```bash
# Increase timeout values
SYNC_TIMEOUT_SECONDS=600
SYNC_CONNECTION_TIMEOUT_SECONDS=60
```

#### 3. Sync Failures
```bash
# Check logs for errors
tail -f storage/logs/laravel.log | grep "AutoSync"

# Retry failed items
php artisan sync:auto --retry-failed --mode=queue-only
```

### Debug Mode

Enable detailed logging:

```bash
php artisan sync:auto --detailed --mode=queue-only
```

### Health Check

Verify system health:

```bash
php artisan sync:auto --health-check --mode=queue-only
```

## Integration

### SyncService

The command integrates with `App\Services\SyncService`:

```php
use App\Services\SyncService;

class YourController
{
    public function __construct(private SyncService $syncService)
    {
        //
    }

    public function manualSync()
    {
        $results = $this->syncService->pushChanges();
        // Handle results
    }
}
```

### Models

Implement sync interfaces in your models:

```php
use App\Contracts\Syncable;

class Product extends Model implements Syncable
{
    public function scopeBySyncStatus($query, $status)
    {
        return $query->where('sync_status', $status);
    }

    public function getSyncData(): array
    {
        return $this->only(['name', 'price', 'stock']);
    }
}
```

### Events

Listen to sync events:

```php
use App\Events\SyncCompleted;
use App\Events\SyncFailed;

Event::listen(SyncCompleted::class, function ($event) {
    // Handle successful sync
});

Event::listen(SyncFailed::class, function ($event) {
    // Handle sync failure
});
```

## Security

### Data Protection

- Sensitive data is encrypted during transmission
- Checksums validate data integrity
- Rate limiting prevents abuse
- Authentication required for sync operations

### Access Control

- Sync operations respect Laravel's authentication
- Role-based access control supported
- Audit trails for all operations

## Performance

### Optimization Tips

1. **Batch Size**: Adjust based on your system's memory
2. **Memory Limits**: Monitor and adjust as needed
3. **Queue Workers**: Use appropriate number of queue workers
4. **Database Indexes**: Ensure proper indexing on sync fields

### Benchmarking

Test performance with different configurations:

```bash
# Test with small batch size
SYNC_BATCH_SIZE=25 php artisan sync:auto --mode=full --detailed

# Test with large batch size
SYNC_BATCH_SIZE=200 php artisan sync:auto --mode=full --detailed
```

## Support

### Documentation

- [Command Documentation](docs/commands/autosync.md)
- [Configuration Reference](config/sync.php)
- [API Documentation](docs/api/sync.md)

### Issues

Report issues on the project's issue tracker or contact the development team.

### Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Add tests
5. Submit a pull request

## License

This sync system is part of the Sales and Inventory Software and follows the same license terms.
