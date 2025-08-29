<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Simple Data Structure Test\n";
echo "==========================\n";

// Check configuration
echo "SYNC_IS_CENTRAL_HUB: " . (config('sync.is_central_hub') ? 'true' : 'false') . "\n";

try {
    // Test the pullChanges method directly
    $syncService = app('App\Services\SyncService');
    
    echo "\nTesting pullChanges() method:\n";
    $pullResults = $syncService->pullChanges();
    
    echo "Pull Results Structure:\n";
    echo "Success: " . $pullResults['success'] . "\n";
    echo "Failed: " . $pullResults['failed'] . "\n";
    echo "Data count: " . count($pullResults['data']) . "\n";
    echo "Errors count: " . count($pullResults['errors']) . "\n";
    
    if (count($pullResults['data']) > 0) {
        echo "\nFirst data item:\n";
        $firstItem = $pullResults['data'][0];
        echo "Type: " . gettype($firstItem) . "\n";
        if (is_array($firstItem)) {
            echo "Keys: " . implode(', ', array_keys($firstItem)) . "\n";
        }
    }
    
    if (count($pullResults['errors']) > 0) {
        echo "\nErrors found:\n";
        foreach ($pullResults['errors'] as $error) {
            echo "- " . $error['error'] . "\n";
        }
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "\nTest completed.\n";
