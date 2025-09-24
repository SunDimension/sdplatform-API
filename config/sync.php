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
    'central_hub_url' => env('SYNC_CENTRAL_HUB_URL', 'https://sync.bfcacademic.com'),
    'api_key' => env('SYNC_API_KEY', ''),
    'location_id' => env('APP_LOCATION_ID', 'default'),
    
    // Central hub identification
    'is_central_hub' => env('SYNC_IS_CENTRAL_HUB', true), // Temporarily set to true for testing
    'central_hub_location_id' => env('SYNC_CENTRAL_HUB_LOCATION_ID', 'CENTRAL_HUB_TEST'),

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
            'sync_fields' => ['*'],
            'exclude_fields' => [],
        ],
        'App\Models\JournalEntryDetail' => [
            'batch_size' => 50,
            'sync_fields' => ['*'],
            'exclude_fields' => [],
        ],
        'App\Models\Transaction' => [
            'batch_size' => 100,
            'sync_fields' => ['*'],
            'exclude_fields' => [],
        ],
        'App\Models\PaymentVoucher' => [
            'batch_size' => 10,
            'sync_fields' => ['*'],
            'exclude_fields' => [],
        ],
        'App\Models\CreateItem' => [
            'batch_size' => 50,
            'sync_fields' => ['*'],
            'exclude_fields' => [],
        ],
        'App\Models\Store' => [
            'batch_size' => 50,
            'sync_fields' => ['*'],
            'exclude_fields' => [],
        ],
        'App\Models\StoreItem' => [
            'batch_size' => 50,
            'sync_fields' => ['*'],
            'exclude_fields' => [],
        ],
        'App\Models\Customer' => [
            'batch_size' => 50,
            'sync_fields' => ['*'],
            'exclude_fields' => [],
        ],
        'App\Models\Vendor' => [
            'batch_size' => 50,
            'sync_fields' => ['*'],
            'exclude_fields' => [],
        ],
        'App\Models\SalesOrder' => [
            'batch_size' => 50,
            'sync_fields' => ['*'],
            'exclude_fields' => [],
        ],
        'App\Models\SalesReceipt' => [
            'batch_size' => 50,
            'sync_fields' => ['*'],
            'exclude_fields' => [],
        ],
        'App\Models\ItemSold' => [
            'batch_size' => 50,
            'sync_fields' => ['*'],
            'exclude_fields' => [],
        ],
        'App\Models\ReceiveOrder' => [
            'batch_size' => 50,
            'sync_fields' => ['*'],
            'exclude_fields' => [],
        ],
        'App\Models\ReceiveItem' => [
            'batch_size' => 50,
            'sync_fields' => ['*'],
            'exclude_fields' => [],
        ],
        'App\Models\StoreTransferOrder' => [
            'batch_size' => 50,
            'sync_fields' => ['*'],
            'exclude_fields' => [],
        ],
        'App\Models\StoreTransferItem' => [
            'batch_size' => 100,
            'sync_fields' => ['*'],
            'exclude_fields' => [],
        ],
        'App\Models\BankRemittance' => [
            'batch_size' => 50,
            'sync_fields' => ['*'],
            'exclude_fields' => [],
        ],
        'App\Models\PostOutflow' => [
            'batch_size' => 50,
            'sync_fields' => ['*'],
            'exclude_fields' => [],
        ],
        'App\Models\PostInflow' => [
            'batch_size' => 50,
            'sync_fields' => ['*'],
            'exclude_fields' => [],
        ],
        'App\Models\Release' => [
            'batch_size' => 50,
            'sync_fields' => ['*'],
            'exclude_fields' => [],
        ],
        'App\Models\ReleaseDetail' => [
            'batch_size' => 50,
            'sync_fields' => ['*'],
            'exclude_fields' => [],
        ],
        'App\Models\ReturnItem' => [
            'batch_size' => 50,
            'sync_fields' => ['*'],
            'exclude_fields' => [],
        ],
        'App\Models\ReturnDetail' => [
            'batch_size' => 50,
            'sync_fields' => ['*'],
            'exclude_fields' => [],
        ],
        'App\Models\PriceChange' => [
            'batch_size' => 50,
            'sync_fields' => ['*'],
            'exclude_fields' => [],
        ],
        'App\Models\CashierRemittance' => [
            'batch_size' => 50,
            'sync_fields' => ['*'],
            'exclude_fields' => [],
        ],
        'App\Models\CashierExpense' => [
            'batch_size' => 50,
            'sync_fields' => ['*'],
            'exclude_fields' => [],
        ],
        'App\Models\User' => [
            'batch_size' => 50,
            'sync_fields' => ['*'],
            'exclude_fields' => [],
        ],
        'App\Models\CreditTransaction' => [
            'batch_size' => 50,
            'sync_fields' => ['*'],
            'exclude_fields' => [],
        ],
        'App\Models\Branch' => [
            'batch_size' => 50,
            'sync_fields' => ['*'],
            'exclude_fields' => [],
        ],
        'App\Models\TransactionJournalEntry' => [
            'batch_size' => 50,
            'sync_fields' => ['*'],
            'exclude_fields' => [],
        ],
        'App\Models\TransactionJournalEntryDetail' => [
            'batch_size' => 50,
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
        'enabled' => env('SYNC_SCHEDULING_ENABLED', true),
        'interval_minutes' => env('SYNC_SCHEDULING_INTERVAL_MINUTES', 15),
        'default_mode' => env('SYNC_SCHEDULING_DEFAULT_MODE', 'full'),
        'high_activity_interval' => env('SYNC_HIGH_ACTIVITY_INTERVAL', 5), // minutes
        'medium_activity_interval' => env('SYNC_MEDIUM_ACTIVITY_INTERVAL', 15), // minutes
        'low_activity_interval' => env('SYNC_LOW_ACTIVITY_INTERVAL', 30), // minutes
        'retry_interval' => env('SYNC_RETRY_INTERVAL', 60), // minutes
        'business_hours' => [
            'start' => env('SYNC_BUSINESS_HOURS_START', 8), // 8 AM
            'end' => env('SYNC_BUSINESS_HOURS_END', 18), // 6 PM
            'days' => env('SYNC_BUSINESS_DAYS', '1,2,3,4,5'), // Monday-Friday
        ],
        'adaptive_scheduling' => env('SYNC_ADAPTIVE_SCHEDULING', true),
        'offline_grace_period' => env('SYNC_OFFLINE_GRACE_PERIOD', 300), // 5 minutes
    ],
]; 