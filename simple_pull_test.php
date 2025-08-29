<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Simple Pull Test as Central Hub\n";
echo "===============================\n";

try {
    $request = new \Illuminate\Http\Request();
    $request->merge([
        'model' => 'Store',
        'last_sync' => '2024-01-01 00:00:00',
        'location_id' => 'TEST_LOCATION_001',
        'batch_size' => 5
    ]);
    
    $syncController = app('App\Http\Controllers\SyncController');
    $response = $syncController->pull($request);
    
    if ($response instanceof \Illuminate\Http\JsonResponse) {
        $data = $response->getData(true);
        echo "Response status: " . $response->getStatusCode() . "\n";
        echo "Total changes: " . count($data['data']['data']) . "\n";
        
        if (count($data['data']['data']) > 0) {
            echo "\nFirst change details:\n";
            $firstChange = $data['data']['data'][0];
            echo "Action: " . $firstChange['action'] . "\n";
            echo "Data: " . json_encode($firstChange['data'], JSON_PRETTY_PRINT) . "\n";
        }
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "\nTest completed.\n";
