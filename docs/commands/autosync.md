# AutoSync Command Documentation

## Overview

The `AutoSync` command is a comprehensive data synchronization tool that provides intelligent, automated synchronization between local and remote systems. It supports multiple sync modes, automatic retry mechanisms, health checks, and detailed reporting.

## Command Signature

```bash
php artisan sync:auto [options]
```

## Options

| Option | Description | Default |
|--------|-------------|---------|
| `--mode` | Sync mode: `full`, `push-only`, `pull-only`, `queue-only` | `full` |
| `--retry-failed` | Retry previously failed sync items | `false` |
| `--health-check` | Perform health check before sync | `false` |
| `--force` | Force sync even when system appears offline | `false` |
| `--detailed` | Show detailed output and progress | `false` |

## Sync Modes

### Full Sync (`--mode=full`)
Executes a complete synchronization cycle:
1. **Push Changes**: Sends local changes to remote system
2. **Pull Changes**: Retrieves changes from remote system
3. **Queue Processing**: Processes any pending sync items

### Push-Only (`--mode=push-only`)
Only sends local changes to the remote system. Useful when:
- You only want to upload local changes
- Remote system is read-only
- Testing push functionality

### Pull-Only (`--mode=pull-only`)
Only retrieves changes from the remote system. Useful when:
- You only want to download remote changes
- Local system is read-only
- Testing pull functionality

### Queue-Only (`--mode=queue-only`)
Only processes pending sync items in the queue. Useful when:
- System appears offline
- You want to process queued items without network operations
- Testing queue processing

## Usage Examples

### Basic Full Synchronization
```bash
php artisan sync:auto
```

### Push-Only with Detailed Output
```bash
php artisan sync:auto --mode=push-only --detailed
```

### Force Sync When Offline
```bash
php artisan sync:auto --force
```

### Health Check Before Sync
```bash
php artisan sync:auto --health-check
```

### Retry Failed Items
```bash
php artisan sync:auto --retry-failed
```

### Complete Example with All Options
```bash
php artisan sync:auto \
    --mode=full \
    --retry-failed \
    --health-check \
    --force \
    --detailed
```

## Configuration

The command uses several configuration options from `config/sync.php`:

```php
return [
    'models' => [
        // Syncable model configurations
    ],
    'batch_size' => env('SYNC_BATCH_SIZE', 100),
    'max_memory_mb' => env('SYNC_MAX_MEMORY_MB', 512),
    'retry_attempts' => env('SYNC_RETRY_ATTEMPTS', 3),
    'timeout_seconds' => env('SYNC_TIMEOUT_SECONDS', 300),
];
```

### Environment Variables
```bash
SYNC_BATCH_SIZE=100
SYNC_MAX_MEMORY_MB=512
SYNC_RETRY_ATTEMPTS=3
SYNC_TIMEOUT_SECONDS=300
```

## Output and Reporting

### Standard Output
```
🤖 Starting automated synchronization...
🔄 Executing full synchronization...
📤 Pushing local changes...
✅ Push completed: 5 success, 0 failed
📥 Pulling changes from central hub...
✅ Pull completed: 3 success, 0 failed
🔄 Processing sync queue...
✅ Queue processing completed: 2 processed, 0 failed
📊 Synchronization Report
========================
✅ Synchronization completed in 1250ms
📈 Results: 10 success, 0 failed
```

### Detailed Output (`--detailed`)
```
🤖 Starting automated synchronization...
📊 Progress: 0/3 operations completed
🔄 Executing full synchronization...
📤 Pushing local changes...
📊 Batch size: 100, Max memory: 512MB
✅ Push completed: 5 success, 0 failed
📊 Progress: 1/3 operations completed
📥 Pulling changes from central hub...
✅ Pull completed: 3 success, 0 failed
📊 Progress: 2/3 operations completed
🔄 Processing sync queue...
📊 Batch size: 100, Max memory: 512MB
✅ Queue processing completed: 2 processed, 0 failed (45.2MB)
📊 Progress: 3/3 operations completed
📊 Synchronization Report
========================
✅ Synchronization completed in 1250ms
📈 Results: 10 success, 0 failed
```

## Error Handling

The command includes comprehensive error handling:

- **Network Failures**: Automatically falls back to queue-only mode
- **Service Exceptions**: Logs errors and continues with other operations
- **Memory Management**: Tracks memory usage and warns about high consumption
- **Retry Mechanism**: Can retry failed items automatically

### Error Output Examples
```
⚠️  System appears offline. Processing queue only...
❌ Push synchronization failed: Connection timeout
⚠️  Continuing with other operations...
```

## Performance Features

### Batch Processing
- Configurable batch sizes for large datasets
- Memory usage monitoring
- Progress tracking for long operations

### Memory Management
- Tracks memory consumption during operations
- Configurable memory limits
- Automatic garbage collection hints

### Progress Tracking
- Real-time progress updates in detailed mode
- Operation completion counters
- Performance metrics

## Integration Points

### SyncService
The command integrates with `App\Services\SyncService` for:
- Push/pull operations
- Queue processing
- Health checks
- Online status verification

### Models
Integrates with syncable models that implement:
- `scopeBySyncStatus` scope
- Sync status tracking
- Batch processing capabilities

### Monitoring
- Logs all operations to Laravel logs
- Caches statistics for monitoring dashboards
- Provides detailed metrics for analysis

## Best Practices

### 1. Scheduling
```bash
# Add to crontab for automated sync every 15 minutes
*/15 * * * * cd /path/to/project && php artisan sync:auto --mode=full
```

### 2. Health Monitoring
```bash
# Check system health before major operations
php artisan sync:auto --health-check --mode=queue-only
```

### 3. Error Recovery
```bash
# Retry failed items after resolving issues
php artisan sync:auto --retry-failed --mode=queue-only
```

### 4. Performance Tuning
- Adjust `SYNC_BATCH_SIZE` based on your system's memory
- Monitor memory usage with `--detailed` flag
- Use appropriate sync modes for different scenarios

## Troubleshooting

### Common Issues

1. **Memory Exceeded**
   - Reduce `SYNC_BATCH_SIZE`
   - Increase `SYNC_MAX_MEMORY_MB`
   - Use `--mode=queue-only` for large datasets

2. **Network Timeouts**
   - Check `SYNC_TIMEOUT_SECONDS`
   - Use `--force` to bypass offline detection
   - Verify network connectivity

3. **Sync Failures**
   - Check logs for detailed error messages
   - Use `--retry-failed` to retry failed items
   - Verify model configurations

### Debug Mode
For debugging, enable detailed logging:
```bash
php artisan sync:auto --detailed --mode=queue-only
```

## Security Considerations

- The command respects Laravel's authentication and authorization
- Sync operations are logged for audit trails
- Sensitive data is not exposed in command output
- Use appropriate file permissions for log files

## Monitoring and Alerting

### Statistics Tracking
The command provides comprehensive statistics:
- Success/failure counts
- Processing duration
- Memory usage
- Operation details

### Log Analysis
```bash
# View recent sync logs
tail -f storage/logs/laravel.log | grep "AutoSync"

# Search for errors
grep "AutoSync.*failed" storage/logs/laravel.log
```

### Performance Metrics
Monitor key metrics:
- Sync duration trends
- Success/failure rates
- Memory usage patterns
- Queue processing times
