<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "🔍 CHECKING BANK_REMITTANCES FOREIGN KEY REFERENCES\n";
echo "==================================================\n\n";

// Check the referenced tables
$referencedTables = ['banks', 'branches', 'stores', 'users'];

foreach ($referencedTables as $table) {
    echo "=== $table ===\n";
    
    if (!DB::getSchemaBuilder()->hasTable($table)) {
        echo "❌ Table does not exist\n\n";
        continue;
    }
    
    $columns = DB::select('SHOW COLUMNS FROM ' . $table);
    foreach ($columns as $col) {
        if ($col->Field === 'id') {
            $status = $col->Type === 'char(36)' ? '✅ UUID' : '❌ INTEGER';
            echo "ID column: {$col->Type} {$status}\n";
        }
    }
    
    // Check sample data
    $sampleData = DB::table($table)->select('id')->limit(3)->get();
    echo "Sample IDs: ";
    foreach ($sampleData as $row) {
        echo $row->id . " ";
    }
    echo "\n\n";
}

// Check bank_remittances table structure and sample data
echo "=== bank_remittances ===\n";
$columns = DB::select('SHOW COLUMNS FROM bank_remittances');
foreach ($columns as $col) {
    if (in_array($col->Field, ['bank_id', 'branch_id', 'store_id', 'user_id'])) {
        echo "{$col->Field}: {$col->Type}\n";
    }
}

echo "\nSample bank_remittances data:\n";
$sampleData = DB::table('bank_remittances')->select('bank_id', 'branch_id', 'store_id', 'user_id')->limit(5)->get();
foreach ($sampleData as $row) {
    echo "bank_id: {$row->bank_id}, branch_id: {$row->branch_id}, store_id: {$row->store_id}, user_id: {$row->user_id}\n";
}

echo "\n🎯 ANALYSIS\n";
echo "===========\n";
echo "Check if foreign key data types match referenced table ID types.\n";
