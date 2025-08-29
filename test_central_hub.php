<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Testing Central Hub Configuration\n";
echo "================================\n";

// Check configuration
echo "SYNC_IS_CENTRAL_HUB: " . (config('sync.is_central_hub') ? 'true' : 'false') . "\n";
echo "CENTRAL_HUB_LOCATION_ID: " . config('sync.central_hub_location_id') . "\n";

// Check store data
echo "\nStore Data:\n";
echo "Store count: " . App\Models\Store::count() . "\n";

if (App\Models\Store::count() > 0) {
    $store = App\Models\Store::first();
    echo "Sample store ID: " . $store->id . "\n";
    echo "Sample store name: " . $store->name . "\n";
    echo "Sample store location_id: " . ($store->location_id ?? 'null') . "\n";
}

// Test sync service
echo "\nTesting Sync Service:\n";
try {
    // Use direct instantiation to avoid DI container issues
    $syncService = new App\Services\SyncService(app('App\Services\SyncNotificationService'));
    echo "Sync service loaded successfully\n";
    
    // Check if it's in central hub mode
    echo "isCentralHub method exists: " . (method_exists($syncService, 'isCentralHub') ? 'true' : 'false') . "\n";
    echo "isCentralHub result: " . ($syncService->isCentralHub() ? 'true' : 'false') . "\n";
    
    // Test sync functionality
    echo "\nTesting Sync Functionality:\n";
    
    // Test if we can check online status
    if (method_exists($syncService, 'isOnline')) {
        echo "isOnline method exists: true\n";
        echo "isOnline result: " . ($syncService->isOnline() ? 'true' : 'false') . "\n";
    }
    
    // Test if we can get sync queue info
    if (method_exists($syncService, 'processQueue')) {
        echo "processQueue method exists: true\n";
    }
    
} catch (Exception $e) {
    echo "Error loading sync service: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\nTest completed.\n";
