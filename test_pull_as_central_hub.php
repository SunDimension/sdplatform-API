<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Testing Pull Functionality as Central Hub\n";
echo "=========================================\n";

// Check configuration
echo "SYNC_IS_CENTRAL_HUB: " . (config('sync.is_central_hub') ? 'true' : 'false') . "\n";
echo "CENTRAL_HUB_LOCATION_ID: " . config('sync.central_hub_location_id') . "\n";

// Test the pull functionality
echo "\nTesting Pull Endpoint:\n";

try {
    // Create a mock request to test the pull functionality
    $request = new \Illuminate\Http\Request();
    $request->merge([
        'model' => 'Store',
        'last_sync' => '2024-01-01 00:00:00',
        'location_id' => 'TEST_LOCATION_001',
        'batch_size' => 10
    ]);
    
    // Get the sync controller
    $syncController = app('App\Http\Controllers\SyncController');
    
    // Test the pull method
    echo "Testing pull method...\n";
    $response = $syncController->pull($request);
    
    if ($response instanceof \Illuminate\Http\JsonResponse) {
        $data = $response->getData(true);
        echo "Response status: " . $response->getStatusCode() . "\n";
        echo "Response data: " . json_encode($data, JSON_PRETTY_PRINT) . "\n";
    } else {
        echo "Response: " . $response . "\n";
    }
    
} catch (Exception $e) {
    echo "Error testing pull: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}

echo "\nTest completed.\n";
