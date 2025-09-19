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
        echo "🔧 FIXING FOREIGN KEY COLUMN TYPES\n";
        echo "===================================\n\n";

        // Step 1: Complete UUID conversion for create_items
        $this->convertCreateItemsToUuid();

        // Step 2: Fix foreign key column types in all tables
        $this->fixForeignKeyColumnTypes();

        echo "\n✅ Foreign key column types fixed successfully!\n";
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        throw new \Exception('This migration cannot be reversed. Restore from backup if needed.');
    }

    private function convertCreateItemsToUuid()
    {
        echo "Converting create_items to UUID...\n";

        if (!Schema::hasTable('create_items')) {
            echo "create_items table does not exist\n";
            return;
        }

        // Disable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        try {
            // Step 1: Add UUID column
            if (!Schema::hasColumn('create_items', 'uuid')) {
                Schema::table('create_items', function (Blueprint $table) {
                    $table->uuid('uuid')->nullable()->after('id');
                });
            }

            // Step 2: Generate UUIDs for existing records
            $records = DB::table('create_items')->whereNull('uuid')->get();
            foreach ($records as $record) {
                DB::table('create_items')
                    ->where('id', $record->id)
                    ->update(['uuid' => (string) Str::uuid()]);
            }

            // Step 3: Add unique constraint
            $indexes = DB::select("SHOW INDEX FROM create_items WHERE Key_name LIKE '%uuid%'");
            if (empty($indexes)) {
                Schema::table('create_items', function (Blueprint $table) {
                    $table->unique('uuid');
                });
            }

            // Step 4: Update foreign key references that point to create_items
            $this->updateForeignKeyReferences('create_items');

            // Step 5: Drop foreign key constraints, drop old id column, rename uuid to id, recreate constraints
            $this->dropForeignKeyConstraints('create_items');

            Schema::table('create_items', function (Blueprint $table) {
                $table->dropColumn('id');
            });

            Schema::table('create_items', function (Blueprint $table) {
                $table->renameColumn('uuid', 'id');
            });

            Schema::table('create_items', function (Blueprint $table) {
                $table->primary('id');
            });

            $this->recreateForeignKeyConstraints('create_items');

            echo "Completed conversion of create_items\n";

        } finally {
            // Re-enable foreign key checks
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        }
    }

    private function fixForeignKeyColumnTypes()
    {
        echo "Fixing foreign key column types...\n";

        // Define tables that have UUID primary keys
        $uuidTables = ['branches', 'stores', 'create_items', 'customers', 'vendors'];

        // Get all foreign key relationships
        $foreignKeys = DB::select("
            SELECT 
                TABLE_NAME,
                COLUMN_NAME,
                REFERENCED_TABLE_NAME,
                REFERENCED_COLUMN_NAME,
                CONSTRAINT_NAME
            FROM 
                INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
            WHERE 
                REFERENCED_TABLE_NAME IN ('" . implode("','", $uuidTables) . "')
                AND REFERENCED_COLUMN_NAME = 'id'
        ");

        foreach ($foreignKeys as $fk) {
            $referencingTable = $fk->TABLE_NAME;
            $referencingColumn = $fk->COLUMN_NAME;
            $referencedTable = $fk->REFERENCED_TABLE_NAME;
            $constraintName = $fk->CONSTRAINT_NAME;

            if (!Schema::hasTable($referencingTable)) {
                echo "Skipping {$referencingTable} - table does not exist\n";
                continue;
            }

            if (!Schema::hasColumn($referencingTable, $referencingColumn)) {
                echo "Skipping {$referencingTable}.{$referencingColumn} - column does not exist\n";
                continue;
            }

            // Check if the referencing column is already UUID type
            $columnInfo = DB::select("SHOW COLUMNS FROM {$referencingTable} WHERE Field = '{$referencingColumn}'");
            if (empty($columnInfo)) {
                echo "Skipping {$referencingTable}.{$referencingColumn} - column info not found\n";
                continue;
            }

            $currentType = $columnInfo[0]->Type;
            if ($currentType === 'char(36)') {
                echo "Skipping {$referencingTable}.{$referencingColumn} - already UUID type\n";
                continue;
            }

            echo "Converting {$referencingTable}.{$referencingColumn} from {$currentType} to char(36)...\n";

            // Disable foreign key checks
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');

            try {
                // Drop the foreign key constraint
                try {
                    Schema::table($referencingTable, function (Blueprint $blueprint) use ($constraintName) {
                        $blueprint->dropForeign($constraintName);
                    });
                    echo "  Dropped foreign key constraint {$constraintName}\n";
                } catch (\Exception $e) {
                    echo "  Skipped dropping constraint {$constraintName} - {$e->getMessage()}\n";
                }

                // Update foreign key values to use UUIDs
                $this->updateForeignKeyValues($referencedTable, $referencingTable, $referencingColumn);

                // Change column type to UUID
                Schema::table($referencingTable, function (Blueprint $table) use ($referencingColumn) {
                    $table->uuid($referencingColumn)->change();
                });

                // Recreate foreign key constraint
                Schema::table($referencingTable, function (Blueprint $blueprint) use ($referencingColumn, $referencedTable, $constraintName) {
                    $blueprint->foreign($referencingColumn)->references('id')->on($referencedTable)->name($constraintName);
                });

                echo "  Recreated foreign key constraint {$constraintName}\n";

            } finally {
                // Re-enable foreign key checks
                DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            }
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
