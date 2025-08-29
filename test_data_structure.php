<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Testing Data Structure from Sync Service\n";
echo "========================================\n";

// Check configuration
echo "SYNC_IS_CENTRAL_HUB: " . (config('sync.is_central_hub') ? 'true' : 'false') . "\n";

try {
    // Get the sync service directly
    $syncService = app('App\Services\SyncService');
    
    echo "\nTesting getCentralHubSyncData() method:\n";
    $centralHubData = $syncService->getCentralHubSyncData();
    
    echo "Central Hub Data Structure:\n";
    echo "Data count: " . count($centralHubData['data']) . "\n";
    echo "Meta: " . json_encode($centralHubData['meta'], JSON_PRETTY_PRINT) . "\n";
    
    if (count($centralHubData['data']) > 0) {
        echo "\nFirst change structure:\n";
        $firstChange = $centralHubData['data'][0];
        echo json_encode($firstChange, JSON_PRETTY_PRINT) . "\n";
        
        // Check if it has required fields
        echo "\nRequired fields check:\n";
        echo "Has model_type: " . (isset($firstChange['model_type']) ? 'Yes' : 'No') . "\n";
        echo "Has action: " . (isset($firstChange['action']) ? 'Yes' : 'No') . "\n";
        echo "Has data: " . (isset($firstChange['data']) ? 'Yes' : 'No') . "\n";
    }
    
    echo "\nTesting pullChanges() method:\n";
    $pullResults = $syncService->pullChanges();
    
    echo "Pull Results Structure:\n";
    echo "Success: " . $pullResults['success'] . "\n";
    echo "Failed: " . $pullResults['failed'] . "\n";
    echo "Data count: " . count($pullResults['data']) . "\n";
    echo "Errors count: " . count($pullResults['errors']) . "\n";
    
    if (count($pullResults['errors']) > 0) {
        echo "\nErrors found:\n";
        foreach ($pullResults['errors'] as $error) {
            echo "- " . $error['error'] . "\n";
        }
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}

echo "\nTest completed.\n";
