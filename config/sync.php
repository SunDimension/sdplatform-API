<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Synchronization Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains configuration options for the data synchronization
    | system, including batch sizes, memory limits, and retry settings.
    |
    */

    /*
    |--------------------------------------------------------------------------
    | Central Hub Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for the central synchronization hub
    |
    */
    'central_hub_url' => env('SYNC_CENTRAL_HUB_URL', 'https://api.example.com'),
    'api_key' => env('SYNC_API_KEY', ''),
    'location_id' => env('APP_LOCATION_ID', 'default'),

    /*
    |--------------------------------------------------------------------------
    | Syncable Models
    |--------------------------------------------------------------------------
    |
    | Define which models should participate in synchronization. Each model
    | should implement the necessary sync interfaces and scopes.
    |
    */
    'models' => [
        'App\Models\JournalEntry' => [
            'batch_size' => 50,
            'sync_fields' => ['description', 'payment_date', 'store_id', 'branch_id', 'vendor_id'],
            'exclude_fields' => ['created_by', 'modified_by', 'deleted_by'],
        ],
        'App\Models\Transaction' => [
            'batch_size' => 100,
            'sync_fields' => ['*'],
            'exclude_fields' => [],
        ],
        'App\Models\PaymentVoucher' => [
            'batch_size' => 75,
            'sync_fields' => ['*'],
            'exclude_fields' => [],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Batch Processing
    |--------------------------------------------------------------------------
    |
    | Configure batch sizes for processing large datasets to optimize
    | memory usage and performance.
    |
    */
    'batch_size' => env('SYNC_BATCH_SIZE', 100),

    /*
    |--------------------------------------------------------------------------
    | Memory Management
    |--------------------------------------------------------------------------
    |
    | Configure memory limits and monitoring for sync operations.
    |
    */
    'max_memory_mb' => env('SYNC_MAX_MEMORY_MB', 512),
    'memory_warning_threshold' => env('SYNC_MEMORY_WARNING_THRESHOLD', 0.8), // 80% of max

    /*
    |--------------------------------------------------------------------------
    | Retry Configuration
    |--------------------------------------------------------------------------
    |
    | Configure retry attempts and delays for failed sync operations.
    |
    */
    'retry_attempts' => env('SYNC_RETRY_ATTEMPTS', 3),
    'retry_delay_seconds' => env('SYNC_RETRY_DELAY_SECONDS', 60),
    'max_retry_delay_seconds' => env('SYNC_MAX_RETRY_DELAY_SECONDS', 3600),

    /*
    |--------------------------------------------------------------------------
    | Timeout Settings
    |--------------------------------------------------------------------------
    |
    | Configure various timeout values for sync operations.
    |
    */
    'timeout_seconds' => env('SYNC_TIMEOUT_SECONDS', 300),
    'connection_timeout_seconds' => env('SYNC_CONNECTION_TIMEOUT_SECONDS', 30),
    'read_timeout_seconds' => env('SYNC_READ_TIMEOUT_SECONDS', 60),

    /*
    |--------------------------------------------------------------------------
    | Health Check Configuration
    |--------------------------------------------------------------------------
    |
    | Configure health check parameters and thresholds.
    |
    */
    'health_check' => [
        'enabled' => env('SYNC_HEALTH_CHECK_ENABLED', true),
        'timeout_seconds' => env('SYNC_HEALTH_CHECK_TIMEOUT', 10),
        'max_failed_attempts' => env('SYNC_HEALTH_CHECK_MAX_FAILED', 3),
        'check_interval_seconds' => env('SYNC_HEALTH_CHECK_INTERVAL', 300),
    ],

    /*
    |--------------------------------------------------------------------------
    | Queue Configuration
    |--------------------------------------------------------------------------
    |
    | Configure queue processing parameters and priorities.
    |
    */
    'queue' => [
        'connection' => env('SYNC_QUEUE_CONNECTION', 'database'),
        'queue_name' => env('SYNC_QUEUE_NAME', 'sync'),
        'max_jobs_per_worker' => env('SYNC_MAX_JOBS_PER_WORKER', 1000),
        'job_timeout_seconds' => env('SYNC_JOB_TIMEOUT_SECONDS', 300),
    ],

    /*
    |--------------------------------------------------------------------------
    | Logging Configuration
    |--------------------------------------------------------------------------
    |
    | Configure logging levels and channels for sync operations.
    |
    */
    'logging' => [
        'level' => env('SYNC_LOG_LEVEL', 'info'),
        'channel' => env('SYNC_LOG_CHANNEL', 'stack'),
        'log_success' => env('SYNC_LOG_SUCCESS', true),
        'log_failures' => env('SYNC_LOG_FAILURES', true),
        'log_performance' => env('SYNC_LOG_PERFORMANCE', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Monitoring Configuration
    |--------------------------------------------------------------------------
    |
    | Configure monitoring and metrics collection for sync operations.
    |
    */
    'monitoring' => [
        'enabled' => env('SYNC_MONITORING_ENABLED', true),
        'metrics_cache_ttl' => env('SYNC_METRICS_CACHE_TTL', 3600), // 1 hour
        'performance_thresholds' => [
            'max_duration_seconds' => env('SYNC_MAX_DURATION_SECONDS', 600),
            'max_memory_usage_mb' => env('SYNC_MAX_MEMORY_USAGE_MB', 1024),
            'max_failure_rate' => env('SYNC_MAX_FAILURE_RATE', 0.1), // 10%
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Cache Configuration
    |--------------------------------------------------------------------------
    |
    | Configure caching for sync statistics and metadata.
    |
    */
    'cache' => [
        'prefix' => env('SYNC_CACHE_PREFIX', 'sync'),
        'ttl' => env('SYNC_CACHE_TTL', 86400), // 24 hours
        'stats_ttl' => env('SYNC_STATS_CACHE_TTL', 604800), // 7 days
    ],

    /*
    |--------------------------------------------------------------------------
    | Security Configuration
    |--------------------------------------------------------------------------
    |
    | Configure security settings for sync operations.
    |
    */
    'security' => [
        'encrypt_sensitive_data' => env('SYNC_ENCRYPT_SENSITIVE_DATA', true),
        'validate_checksums' => env('SYNC_VALIDATE_CHECKSUMS', true),
        'max_concurrent_syncs' => env('SYNC_MAX_CONCURRENT_SYNCS', 5),
        'rate_limit_per_minute' => env('SYNC_RATE_LIMIT_PER_MINUTE', 100),
    ],

    /*
    |--------------------------------------------------------------------------
    | Notification Configuration
    |--------------------------------------------------------------------------
    |
    | Configure notifications for sync events and failures.
    |
    */
    'notifications' => [
        'enabled' => env('SYNC_NOTIFICATIONS_ENABLED', false),
        'channels' => env('SYNC_NOTIFICATION_CHANNELS', ['mail']),
        'recipients' => env('SYNC_NOTIFICATION_RECIPIENTS', []),
        'events' => [
            'sync_started' => env('SYNC_NOTIFY_STARTED', false),
            'sync_completed' => env('SYNC_NOTIFY_COMPLETED', false),
            'sync_failed' => env('SYNC_NOTIFY_FAILED', true),
            'health_check_failed' => env('SYNC_NOTIFY_HEALTH_FAILED', true),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Scheduling Configuration
    |--------------------------------------------------------------------------
    |
    | Configure automated synchronization scheduling.
    |
    */
    'scheduling' => [
        'enabled' => env('SYNC_SCHEDULING_ENABLED', false),
        'interval_minutes' => env('SYNC_SCHEDULING_INTERVAL_MINUTES', 15),
        'default_mode' => env('SYNC_SCHEDULING_DEFAULT_MODE', 'full'),
    ],
]; 