<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "🔍 CHECKING REMAINING TABLES\n";
echo "============================\n\n";

$tables = ['stores', 'branches', 'create_items'];

foreach ($tables as $table) {
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
    
    // Check for foreign key references to this table
    echo "Foreign key references:\n";
    $foreignKeys = DB::select("
        SELECT 
            TABLE_NAME,
            COLUMN_NAME,
            CONSTRAINT_NAME
        FROM 
            INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
        WHERE 
            REFERENCED_TABLE_NAME = '{$table}' 
            AND REFERENCED_COLUMN_NAME = 'id'
    ");
    
    foreach ($foreignKeys as $fk) {
        echo "  - {$fk->TABLE_NAME}.{$fk->COLUMN_NAME} ({$fk->CONSTRAINT_NAME})\n";
    }
    
    echo "\n";
}

echo "🎯 SUMMARY\n";
echo "==========\n";
echo "Check if any tables still have integer IDs that need UUID conversion.\n";
