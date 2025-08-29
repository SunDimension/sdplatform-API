<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Debug Central Hub Detection\n";
echo "===========================\n";

// Check configuration
echo "SYNC_IS_CENTRAL_HUB: " . (config('sync.is_central_hub') ? 'true' : 'false') . "\n";
echo "CENTRAL_HUB_LOCATION_ID: " . config('sync.central_hub_location_id') . "\n";

try {
    $syncService = app('App\Services\SyncService');
    
    echo "\nTesting isCentralHub() method:\n";
    $isCentralHub = $syncService->isCentralHub();
    echo "isCentralHub() returns: " . ($isCentralHub ? 'true' : 'false') . "\n";
    
    // Check the internal properties
    echo "\nInternal service properties:\n";
    $reflection = new ReflectionClass($syncService);
    
    // Try to access protected properties
    $locationIdProperty = $reflection->getProperty('locationId');
    $locationIdProperty->setAccessible(true);
    echo "locationId: " . $locationIdProperty->getValue($syncService) . "\n";
    
    $centralHubUrlProperty = $reflection->getProperty('centralHubUrl');
    $centralHubUrlProperty->setAccessible(true);
    echo "centralHubUrl: " . $centralHubUrlProperty->getValue($syncService) . "\n";
    
    // Check request host
    echo "\nRequest host: " . request()->getHost() . "\n";
    
    // Check parsed hub host
    $hubHost = parse_url(config('sync.central_hub_url'), PHP_URL_HOST);
    echo "Hub host: " . $hubHost . "\n";
    
    // Check location ID comparison
    $centralHubLocationId = config('sync.central_hub_location_id');
    $currentLocationId = $locationIdProperty->getValue($syncService);
    echo "Current location ID: " . $currentLocationId . "\n";
    echo "Central hub location ID: " . $centralHubLocationId . "\n";
    echo "Location ID match: " . ($currentLocationId == $centralHubLocationId ? 'true' : 'false') . "\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "\nTest completed.\n";
