<?php

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\Http;

echo "Testing Central Hub API...\n";

// Test base endpoint
try {
    $response = Http::get('https://api.bfcacademic.com/api');
    echo "Base API Response: " . $response->status() . "\n";
    echo "Body: " . substr($response->body(), 0, 200) . "\n\n";
} catch (Exception $e) {
    echo "Base API Error: " . $e->getMessage() . "\n\n";
}

// Test health endpoint
try {
    $response = Http::get('https://api.bfcacademic.com/api/status');
    echo "Health Check Response: " . $response->status() . "\n";
    echo "Body: " . substr($response->body(), 0, 200) . "\n\n";
} catch (Exception $e) {
    echo "Health Check Error: " . $e->getMessage() . "\n\n";
}

// Test sync endpoint
try {
    $response = Http::get('https://api.bfcacademic.com/api/sync');
    echo "Sync Endpoint Response: " . $response->status() . "\n";
    echo "Body: " . substr($response->body(), 0, 200) . "\n\n";
} catch (Exception $e) {
    echo "Sync Endpoint Error: " . $e->getMessage() . "\n\n";
}

echo "Test completed.\n"; 