<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Debugging Sync Service\n";
echo "======================\n";

// Check if the class exists
echo "Class exists check:\n";
echo "App\\Services\\SyncService exists: " . (class_exists('App\Services\SyncService') ? 'true' : 'false') . "\n";

// Check if we can instantiate it
echo "\nTrying to instantiate SyncService:\n";
try {
    $syncService = new App\Services\SyncService(app('App\Services\SyncNotificationService'));
    echo "✓ SyncService instantiated successfully\n";
    
    // Check the class methods
    echo "\nClass methods:\n";
    $methods = get_class_methods($syncService);
    foreach ($methods as $method) {
        echo "- $method\n";
    }
    
    // Check if isCentralHub method exists
    echo "\nMethod existence check:\n";
    echo "isCentralHub exists: " . (method_exists($syncService, 'isCentralHub') ? 'true' : 'false') . "\n";
    
    // Try to call isCentralHub
    if (method_exists($syncService, 'isCentralHub')) {
        echo "✓ Calling isCentralHub():\n";
        $result = $syncService->isCentralHub();
        echo "Result: " . ($result ? 'true' : 'false') . "\n";
    } else {
        echo "✗ isCentralHub method not found\n";
    }
    
    // Check the actual class name
    echo "\nActual class info:\n";
    echo "Class name: " . get_class($syncService) . "\n";
    echo "Class file: " . (new ReflectionClass($syncService))->getFileName() . "\n";
    
} catch (Exception $e) {
    echo "✗ Error instantiating SyncService: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

// Check configuration
echo "\nConfiguration check:\n";
echo "SYNC_IS_CENTRAL_HUB: " . (config('sync.is_central_hub') ? 'true' : 'false') . "\n";
echo "CENTRAL_HUB_LOCATION_ID: " . config('sync.central_hub_location_id') . "\n";
echo "APP_LOCATION_ID: " . config('app.location_id') . "\n";

echo "\nDebug completed.\n";
