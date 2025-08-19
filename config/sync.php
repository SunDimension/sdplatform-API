<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Central Hub Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for the central synchronization hub
    |
    */
    'central_hub_url' => env('SYNC_CENTRAL_HUB_URL', 'https://sync.example.com'),
    'api_key' => env('SYNC_API_KEY'),
    
    /*
    |--------------------------------------------------------------------------
    | Location Configuration
    |--------------------------------------------------------------------------
    |
    | Each location should have a unique identifier
    |
    */
    'location_id' => env('APP_LOCATION_ID', 'default'),
    
    /*
    |--------------------------------------------------------------------------
    | Synchronization Settings
    |--------------------------------------------------------------------------
    |
    | General synchronization behavior settings
    |
    */
    'batch_size' => env('SYNC_BATCH_SIZE', 50),
    'max_retries' => env('SYNC_MAX_RETRIES', 3),
    'retry_delay' => env('SYNC_RETRY_DELAY', 60), // seconds
    
    /*
    |--------------------------------------------------------------------------
    | Model Synchronization
    |--------------------------------------------------------------------------
    |
    | Configure which models should be synchronized and their priorities
    |
    */
    'models' => [
        'App\Models\JournalEntry' => [
            'priority' => 10,
            'sync_fields' => ['description', 'payment_date', 'store_id', 'branch_id', 'vendor_id'],
            'exclude_fields' => ['created_by', 'modified_by', 'deleted_by'],
        ],
        'App\Models\Transaction' => [
            'priority' => 10,
            'sync_fields' => ['*'], // All fields
            'exclude_fields' => [],
        ],
        'App\Models\PaymentVoucher' => [
            'priority' => 10,
            'sync_fields' => ['*'],
            'exclude_fields' => [],
        ],
        'App\Models\Customer' => [
            'priority' => 8,
            'sync_fields' => ['*'],
            'exclude_fields' => [],
        ],
        'App\Models\Vendor' => [
            'priority' => 8,
            'sync_fields' => ['*'],
            'exclude_fields' => [],
        ],
        'App\Models\Store' => [
            'priority' => 9,
            'sync_fields' => ['*'],
            'exclude_fields' => [],
        ],
        'App\Models\Branch' => [
            'priority' => 9,
            'sync_fields' => ['*'],
            'exclude_fields' => [],
        ],
    ],
    
    /*
    |--------------------------------------------------------------------------
    | Conflict Resolution
    |--------------------------------------------------------------------------
    |
    | How to handle conflicts between local and remote data
    |
    */
    'conflict_resolution' => [
        'strategy' => 'timestamp', // timestamp, manual, location_priority
        'location_priorities' => [
            'headquarters' => 100,
            'main_warehouse' => 90,
            'branch_1' => 80,
            'branch_2' => 70,
        ],
    ],
    
    /*
    |--------------------------------------------------------------------------
    | Queue Configuration
    |--------------------------------------------------------------------------
    |
    | Settings for the synchronization queue
    |
    */
    'queue' => [
        'connection' => env('SYNC_QUEUE_CONNECTION', 'database'),
        'table' => 'sync_queues',
        'max_attempts' => 3,
        'backoff_multiplier' => 3, // 1min, 3min, 9min
    ],
    
    /*
    |--------------------------------------------------------------------------
    | Health Check
    |--------------------------------------------------------------------------
    |
    | Health check settings for determining online status
    |
    */
    'health_check' => [
        'endpoint' => '/health',
        'timeout' => 5, // seconds
        'interval' => 60, // seconds
    ],
    
    /*
    |--------------------------------------------------------------------------
    | Logging
    |--------------------------------------------------------------------------
    |
    | Logging configuration for synchronization activities
    |
    */
    'logging' => [
        'channel' => env('SYNC_LOG_CHANNEL', 'daily'),
        'level' => env('SYNC_LOG_LEVEL', 'info'),
        'include_payload' => env('SYNC_LOG_PAYLOAD', false),
    ],
]; 