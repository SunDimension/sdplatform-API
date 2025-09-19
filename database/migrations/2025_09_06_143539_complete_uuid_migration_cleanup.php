<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Tables that still need UUID conversion (based on our check)
        $remainingTables = ['stores', 'customers', 'vendors', 'branches', 'create_items'];
        
        foreach ($remainingTables as $table) {
            if (Schema::hasTable($table)) {
                $this->convertTableToUuid($table);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // This migration is not easily reversible
        throw new \Exception('This migration cannot be reversed. Restore from backup if needed.');
    }
    
    private function convertTableToUuid(string $table)
    {
        echo "Converting {$table} to UUID...\n";
        
        // Disable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        try {
            // Step 1: Add UUID column
            if (!Schema::hasColumn($table, 'uuid')) {
                Schema::table($table, function (Blueprint $table) {
                    $table->uuid('uuid')->nullable()->after('id');
                });
            }
            
            // Step 2: Generate UUIDs for existing records
            $records = DB::table($table)->whereNull('uuid')->get();
            foreach ($records as $record) {
                DB::table($table)
                    ->where('id', $record->id)
                    ->update(['uuid' => (string) Str::uuid()]);
            }
            
            // Step 3: Add unique constraint
            $indexes = DB::select("SHOW INDEX FROM {$table} WHERE Key_name LIKE '%uuid%'");
            if (empty($indexes)) {
                Schema::table($table, function (Blueprint $table) {
                    $table->unique('uuid');
                });
            }
            
            // Step 4: Update foreign key references that point to this table
            $this->updateForeignKeyReferences($table);
            
            // Step 5: Drop foreign key constraints, drop old id column, rename uuid to id, recreate constraints
            $this->dropForeignKeyConstraints($table);
            
            Schema::table($table, function (Blueprint $table) {
                $table->dropColumn('id');
            });
            
            Schema::table($table, function (Blueprint $table) {
                $table->renameColumn('uuid', 'id');
            });
            
            Schema::table($table, function (Blueprint $table) {
                $table->primary('id');
            });
            
            $this->recreateForeignKeyConstraints($table);
            
            echo "Completed conversion of {$table}\n";
            
        } finally {
            // Re-enable foreign key checks
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        }
    }
    
    private function updateForeignKeyReferences(string $table)
    {
        // Get all foreign key constraints that reference this table
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
            $referencingTable = $fk->TABLE_NAME;
            $referencingColumn = $fk->COLUMN_NAME;
            
            // Update the foreign key values to use UUIDs
            $this->updateForeignKeyValues($table, $referencingTable, $referencingColumn);
        }
    }
    
    private function updateForeignKeyValues(string $referencedTable, string $referencingTable, string $referencingColumn)
    {
        // Check if referencing table exists
        if (!Schema::hasTable($referencingTable)) {
            echo "Skipping {$referencingTable} - table does not exist\n";
            return;
        }
        
        // Check if referencing column exists
        if (!Schema::hasColumn($referencingTable, $referencingColumn)) {
            echo "Skipping {$referencingTable}.{$referencingColumn} - column does not exist\n";
            return;
        }
        
        // Get mapping of old id to new uuid
        $mapping = DB::table($referencedTable)
            ->select('id', 'uuid')
            ->get()
            ->pluck('uuid', 'id')
            ->toArray();
        
        // Update foreign key references
        foreach ($mapping as $oldId => $newUuid) {
            DB::table($referencingTable)
                ->where($referencingColumn, $oldId)
                ->update([$referencingColumn => $newUuid]);
        }
        
        echo "Updated foreign key references in {$referencingTable}.{$referencingColumn}\n";
    }
    
    private function dropForeignKeyConstraints(string $table)
    {
        // Get all foreign key constraints that reference this table
        $foreignKeys = DB::select("
            SELECT 
                TABLE_NAME,
                CONSTRAINT_NAME
            FROM 
                INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
            WHERE 
                REFERENCED_TABLE_NAME = '{$table}' 
                AND REFERENCED_COLUMN_NAME = 'id'
        ");
        
        foreach ($foreignKeys as $fk) {
            $referencingTable = $fk->TABLE_NAME;
            $constraintName = $fk->CONSTRAINT_NAME;
            
            if (Schema::hasTable($referencingTable)) {
                try {
                    Schema::table($referencingTable, function (Blueprint $blueprint) use ($constraintName) {
                        $blueprint->dropForeign($constraintName);
                    });
                    echo "Dropped foreign key constraint {$constraintName}\n";
                } catch (\Exception $e) {
                    echo "Skipped dropping constraint {$constraintName} - {$e->getMessage()}\n";
                }
            }
        }
    }
    
    private function recreateForeignKeyConstraints(string $table)
    {
        // Get all foreign key constraints that reference this table
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
            $referencingTable = $fk->TABLE_NAME;
            $referencingColumn = $fk->COLUMN_NAME;
            $constraintName = $fk->CONSTRAINT_NAME;
            
            if (Schema::hasTable($referencingTable) && Schema::hasColumn($referencingTable, $referencingColumn)) {
                Schema::table($referencingTable, function (Blueprint $blueprint) use ($referencingColumn, $table, $constraintName) {
                    $blueprint->foreign($referencingColumn)->references('id')->on($table)->name($constraintName);
                });
                echo "Recreated foreign key constraint {$constraintName}\n";
            }
        }
    }
};
