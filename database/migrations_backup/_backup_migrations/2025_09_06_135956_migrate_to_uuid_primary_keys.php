<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up()
    {
        $startTime = microtime(true);
        echo "🚀 Starting UUID Migration Process...\n";
        echo "=====================================\n";
        echo "⏰ Start Time: " . date('Y-m-d H:i:s') . "\n\n";
        
        // Disable foreign key checks temporarily
        echo "📋 Step 1: Disabling foreign key checks...\n";
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        echo "✅ Foreign key checks disabled\n\n";
        
        // Step 1: Add UUID columns to all tables
        echo "📋 Step 2: Adding UUID columns to tables...\n";
        $tables = array (
            0 => 'banks',
            1 => 'bank_remittances',
            2 => 'branches',
            3 => 'cashier_expenses',
            4 => 'cashier_remittances',
            5 => 'create_items',
            6 => 'credit_limits',
            7 => 'credit_sales',
            8 => 'credit_transactions',
            9 => 'customers',
            10 => 'expense_lines',
            11 => 'item_categories',
            12 => 'item_types',
            13 => 'item_solds',
            14 => 'payment_vouchers',
            15 => 'payment_voucher_details',
            16 => 'post_inflows',
            17 => 'price_changes',
            18 => 'receive_items',
            19 => 'receive_orders',
            20 => 'releases',
            21 => 'release_details',
            22 => 'sales_orders',
            23 => 'sales_receipts',
            24 => 'settle_credit',
            25 => 'stores',
            26 => 'store_items',
            27 => 'users',
            28 => 'vendors',
            29 => 'vendor_credits',
            30 => 'vendor_target',
            31 => 'years',
        );
        
        $totalTables = count($tables);
        $processedTables = 0;
        
        foreach ($tables as $table) {
            if (Schema::hasTable($table)) {
                // Check if uuid column already exists
                if (!Schema::hasColumn($table, 'uuid')) {
                    echo "  ➤ Adding UUID column to table: {$table}\n";
                    Schema::table($table, function (Blueprint $table) {
                        $table->uuid('uuid')->nullable()->after('id');
                    });
                    echo "    ✅ UUID column added to {$table}\n";
                } else {
                    echo "  ⏭️  UUID column already exists in table: {$table}\n";
                }
                $processedTables++;
            } else {
                echo "  ⚠️  Table {$table} does not exist, skipping...\n";
            }
        }
        
        echo "✅ Step 2 Complete: Added UUID columns to {$processedTables}/{$totalTables} tables\n\n";
        
        try {
            // Step 3: Update foreign key columns to UUID type
            echo "📋 Step 3: Updating foreign key columns to UUID type...\n";
            $this->updateForeignKeyColumns();
            echo "✅ Step 3 Complete: Foreign key columns updated\n\n";
            
            // Step 4: Generate UUIDs for existing records
            echo "📋 Step 4: Generating UUIDs for existing records...\n";
            $this->generateUuidsForExistingRecords($tables);
            echo "✅ Step 4 Complete: UUIDs generated for existing records\n\n";
            
            // Step 5: Add unique constraint to UUID columns
            echo "📋 Step 5: Adding unique constraints to UUID columns...\n";
            $this->addUniqueConstraintsToUuidColumns($tables);
            echo "✅ Step 5 Complete: Unique constraints added to UUID columns\n\n";
            
            // Step 6: Update foreign key references
            echo "📋 Step 6: Updating foreign key references...\n";
            $this->updateForeignKeyReferences($tables);
            echo "✅ Step 6 Complete: Foreign key references updated\n\n";
            
            // Step 7: Drop old id columns and rename uuid to id
            echo "📋 Step 7: Replacing old ID columns with UUID columns...\n";
            $this->replaceIdWithUuid($tables);
            echo "✅ Step 7 Complete: ID columns replaced with UUID columns\n\n";
            
            // Re-enable foreign key checks
            echo "📋 Final Step: Re-enabling foreign key checks...\n";
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            echo "✅ Foreign key checks re-enabled\n\n";
            
        } catch (\Exception $e) {
            echo "❌ Migration failed with error: " . $e->getMessage() . "\n";
            echo "🔄 Re-enabling foreign key checks...\n";
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            echo "✅ Foreign key checks re-enabled\n";
            throw $e;
        }
        
        $endTime = microtime(true);
        $executionTime = round($endTime - $startTime, 2);
        
        echo "🎉 UUID Migration Process Completed Successfully!\n";
        echo "================================================\n";
        echo "⏰ End Time: " . date('Y-m-d H:i:s') . "\n";
        echo "⏱️  Total Execution Time: {$executionTime} seconds\n";
        echo "================================================\n";
    }

    public function down()
    {
        // This migration is not easily reversible
        // You would need to restore from backup
        throw new \Exception('This migration cannot be reversed. Restore from backup if needed.');
    }
    
    private function updateForeignKeyColumns()
    {
        // Update foreign key columns to UUID type
        $foreignKeyColumns = [
            // Add your foreign key columns here
            // Example: 'stores' => ['branch_id', 'store_type_id'],
        ];
        
        if (empty($foreignKeyColumns)) {
            echo "  ⏭️  No foreign key columns to update\n";
            return;
        }
        
        $totalColumns = 0;
        $processedColumns = 0;
        
        // Count total columns
        foreach ($foreignKeyColumns as $table => $columns) {
            $totalColumns += count($columns);
        }
        
        echo "  📊 Total foreign key columns to update: {$totalColumns}\n";
        
        foreach ($foreignKeyColumns as $table => $columns) {
            if (Schema::hasTable($table)) {
                echo "  ➤ Processing table: {$table}\n";
                Schema::table($table, function (Blueprint $table) use ($columns, &$processedColumns) {
                    foreach ($columns as $column) {
                        echo "    ➤ Updating column {$column} to UUID type\n";
                        $table->uuid($column)->change();
                        echo "    ✅ Column {$column} updated to UUID type\n";
                        $processedColumns++;
                    }
                });
            } else {
                echo "  ⚠️  Table {$table} does not exist, skipping...\n";
            }
        }
        
        echo "  📈 Processed {$processedColumns}/{$totalColumns} foreign key columns\n";
    }
    
    private function generateUuidsForExistingRecords(array $tables)
    {
        $totalTables = count($tables);
        $processedTables = 0;
        
        foreach ($tables as $table) {
            if (Schema::hasTable($table)) {
                echo "  ➤ Processing table: {$table}\n";
                $records = DB::table($table)->whereNull('uuid')->get();
                $recordCount = $records->count();
                
                if ($recordCount > 0) {
                    echo "    📊 Found {$recordCount} records without UUIDs\n";
                    
                    $processedRecords = 0;
                    foreach ($records as $record) {
                        // If id is already a UUID, copy it to uuid column
                        if (preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i', $record->id)) {
                            DB::table($table)
                                ->where('id', $record->id)
                                ->update(['uuid' => $record->id]);
                        } else {
                            // Generate new UUID
                            DB::table($table)
                                ->where('id', $record->id)
                                ->update(['uuid' => (string) Str::uuid()]);
                        }
                        $processedRecords++;
                        
                        // Show progress every 100 records
                        if ($processedRecords % 100 === 0) {
                            echo "    ⏳ Processed {$processedRecords}/{$recordCount} records...\n";
                        }
                    }
                    echo "    ✅ Generated UUIDs for {$processedRecords} records in {$table}\n";
                } else {
                    echo "    ⏭️  No records need UUID generation in {$table}\n";
                }
                $processedTables++;
            }
        }
        
        echo "  📈 Processed {$processedTables}/{$totalTables} tables for UUID generation\n";
    }
    
    private function addUniqueConstraintsToUuidColumns(array $tables)
    {
        $totalTables = count($tables);
        $processedTables = 0;
        
        foreach ($tables as $table) {
            if (Schema::hasTable($table)) {
                echo "  ➤ Processing table: {$table}\n";
                // Check if unique constraint already exists
                $indexes = DB::select("SHOW INDEX FROM {$table} WHERE Key_name LIKE '%uuid%'");
                if (empty($indexes)) {
                    echo "    ➤ Adding unique constraint to UUID column\n";
                    Schema::table($table, function (Blueprint $table) {
                        $table->unique('uuid');
                    });
                    echo "    ✅ Unique constraint added to {$table}\n";
                } else {
                    echo "    ⏭️  Unique constraint already exists in {$table}\n";
                }
                $processedTables++;
            }
        }
        
        echo "  📈 Processed {$processedTables}/{$totalTables} tables for unique constraints\n";
    }
    
    private function updateForeignKeyReferences(array $tables)
    {
        // Update foreign key references to use UUIDs
        // This is a complex operation that depends on your specific relationships
        // You may need to customize this based on your database structure
        
        $foreignKeyMappings = [
            // Foreign key mappings for tables being converted to UUID
            'stores' => ['branch_id' => 'branches'],
            'users' => ['branch_id' => 'branches', 'store_id'=>"stores"],
            'create_items' => ['vendor_id' => 'vendors', 'user_id' => 'users', 'item_category_id'=>'item_categories' , 'item_type_id'=>'item_types'],
            'bank_remittances' => ['branch_id' => 'branches', 'store_id' => 'stores', 'user_id' => 'users', 'bank_id' => 'banks'],
            'cashier_expenses' => ['store_id' => 'stores', 'user_id' => 'users'],
            'credit_sales' => ['customer_id' => 'customers', 'branch_id' => 'branches', 'product_id' => 'create_items'],
            'credit_transactions' => ['customer_id' => 'customers', 'branch_id' => 'branches', 'created_by' => 'users', 'modified_by' => 'users', 'deleted_by' => 'users'],
            'item_solds' => ['product_id' => 'create_items', 'store_id' => 'stores', 'sales_order_id'=> 'sales_orders'],
            'payment_vouchers' => ['vendor_id' => 'vendors', 'user_id' => 'users'],
            'receive_orders' => ['vendor_id' => 'vendors', 'store_id' => 'stores', 'branch_id' => 'branches', 'user_id' => 'users'],
            'sales_orders' => ['customer_id' => 'customers', 'store_id' => 'stores', 'branch_id' => 'branches', 'user_id' => 'users'],
            'sales_receipts' => ['customer_id' => 'customers', 'store_id' => 'stores', 'branch_id' => 'branches', 'user_id' => 'users'],
            'store_items' => ['store_id' => 'stores', 'create_item_id' => 'create_items', 'branch_id' => 'branches'],
            'vendor_credits' => ['vendor_id' => 'vendors', 'user_id' => 'users'],
        ];
        
        $totalMappings = 0;
        $processedMappings = 0;
        
        // Count total mappings
        foreach ($foreignKeyMappings as $table => $mappings) {
            $totalMappings += count($mappings);
        }
        
        echo "  📊 Total foreign key mappings to process: {$totalMappings}\n";
        
        foreach ($foreignKeyMappings as $table => $mappings) {
            if (Schema::hasTable($table)) {
                echo "  ➤ Processing table: {$table}\n";
                foreach ($mappings as $foreignKey => $referencedTable) {
                    echo "    ➤ Updating {$foreignKey} -> {$referencedTable}\n";
                    $this->updateForeignKeyValues($table, $foreignKey, $referencedTable);
                    $processedMappings++;
                    
                    // Show progress every 10 mappings
                    if ($processedMappings % 10 === 0) {
                        echo "    ⏳ Processed {$processedMappings}/{$totalMappings} foreign key mappings...\n";
                    }
                }
            } else {
                echo "  ⚠️  Table {$table} does not exist, skipping foreign key mappings...\n";
            }
        }
        
        echo "  📈 Processed {$processedMappings}/{$totalMappings} foreign key mappings\n";
    }
    
    private function updateForeignKeyValues(string $table, string $foreignKey, string $referencedTable)
    {
        // Check if the target table and foreign key column exist
        if (!Schema::hasTable($table) || !Schema::hasColumn($table, $foreignKey)) {
            echo "      ⚠️  Column {$foreignKey} does not exist in table {$table}, skipping...\n";
            return;
        }
        
        if (!Schema::hasTable($referencedTable)) {
            echo "      ⚠️  Referenced table {$referencedTable} does not exist, skipping...\n";
            return;
        }
        
        // Get all records with foreign key values
        $records = DB::table($table)
            ->whereNotNull($foreignKey)
            ->select('id', $foreignKey)
            ->get();
        
        $recordCount = $records->count();
        if ($recordCount > 0) {
            echo "      📊 Found {$recordCount} records to update\n";
            
            $updatedCount = 0;
            foreach ($records as $record) {
                // Find the UUID for the referenced record
                $referencedUuid = DB::table($referencedTable)
                    ->where('id', $record->{$foreignKey})
                    ->value('uuid');
                
                if ($referencedUuid) {
                    // Update the foreign key to use UUID
                    DB::table($table)
                        ->where('id', $record->id)
                        ->update([$foreignKey => $referencedUuid]);
                    $updatedCount++;
                }
            }
            echo "      ✅ Updated {$updatedCount}/{$recordCount} foreign key references\n";
        } else {
            echo "      ⏭️  No records to update\n";
        }
    }
    
    private function replaceIdWithUuid(array $tables)
    {
        $totalTables = count($tables);
        $processedTables = 0;
        
        foreach ($tables as $table) {
            if (Schema::hasTable($table)) {
                echo "  ➤ Processing table: {$table}\n";
                
                try {
                    // Drop the old id column
                    echo "    ➤ Dropping old ID column\n";
                    Schema::table($table, function (Blueprint $table) {
                        $table->dropColumn('id');
                    });
                    echo "    ✅ Old ID column dropped\n";
                    
                    // Rename uuid column to id
                    echo "    ➤ Renaming UUID column to ID\n";
                    Schema::table($table, function (Blueprint $table) {
                        $table->renameColumn('uuid', 'id');
                    });
                    echo "    ✅ UUID column renamed to ID\n";
                    
                    // Make id the primary key
                    echo "    ➤ Setting ID as primary key\n";
                    Schema::table($table, function (Blueprint $table) {
                        $table->primary('id');
                    });
                    echo "    ✅ ID set as primary key\n";
                    
                    echo "    🎉 Successfully converted {$table} to UUID primary key\n";
                } catch (\Exception $e) {
                    echo "    ❌ Error processing {$table}: " . $e->getMessage() . "\n";
                }
                
                $processedTables++;
            }
        }
        
        echo "  📈 Processed {$processedTables}/{$totalTables} tables for ID replacement\n";
    }
};