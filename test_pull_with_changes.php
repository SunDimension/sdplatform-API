<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Testing Pull Functionality with Data Changes\n";
echo "============================================\n";

// Check configuration
echo "SYNC_IS_CENTRAL_HUB: " . (config('sync.is_central_hub') ? 'true' : 'false') . "\n";

// First, let's check current store data
echo "\nCurrent Store Data:\n";
echo "Total stores: " . App\Models\Store::count() . "\n";

// Get a sample store to modify
$store = App\Models\Store::first();
if ($store) {
    echo "Sample store ID: " . $store->id . "\n";
    echo "Sample store name: " . $store->name . "\n";
    echo "Sample store updated_at: " . $store->updated_at . "\n";
    
    // Modify the store to create a change
    $originalName = $store->name;
    $store->name = "Test Pull - " . date('H:i:s');
    $store->save();
    
    echo "Modified store name to: " . $store->name . "\n";
    echo "New updated_at: " . $store->updated_at . "\n";
    
    // Now test the pull functionality
    echo "\nTesting Pull with Recent Changes:\n";
    
    try {
        $request = new \Illuminate\Http\Request();
        $request->merge([
            'model' => 'Store',
            'last_sync' => $store->updated_at->subMinutes(5)->toDateTimeString(), // 5 minutes ago
            'location_id' => 'TEST_LOCATION_001',
            'batch_size' => 10
        ]);
        
        $syncController = app('App\Http\Controllers\SyncController');
        $response = $syncController->pull($request);
        
        if ($response instanceof \Illuminate\Http\JsonResponse) {
            $data = $response->getData(true);
            echo "Response status: " . $response->getStatusCode() . "\n";
            echo "Response data: " . json_encode($data, JSON_PRETTY_PRINT) . "\n";
            
            // Check if the modified store is in the response
            if (isset($data['data']['data']) && is_array($data['data']['data'])) {
                echo "\nChanges found: " . count($data['data']['data']) . "\n";
                foreach ($data['data']['data'] as $change) {
                    echo "- Action: " . $change['action'] . "\n";
                    if (isset($change['data']['id'])) {
                        echo "  Store ID: " . $change['data']['id'] . "\n";
                    }
                    if (isset($change['data']['name'])) {
                        echo "  Store Name: " . $change['data']['name'] . "\n";
                    }
                    if (isset($change['data']['sync_metadata'])) {
                        echo "  Sync Version: " . $change['data']['sync_metadata']['sync_version'] . "\n";
                        echo "  Location ID: " . $change['data']['sync_metadata']['location_id'] . "\n";
                    }
                    echo "\n";
                }
            }
        }
        
    } catch (Exception $e) {
        echo "Error testing pull: " . $e->getMessage() . "\n";
    }
    
    // Restore original name
    $store->name = $originalName;
    $store->save();
    echo "\nRestored original store name: " . $store->name . "\n";
    
} else {
    echo "No stores found to test with.\n";
}

echo "\nTest completed.\n";
