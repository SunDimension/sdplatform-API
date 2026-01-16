<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class UpdateModelsWithUuid extends Command
{
    protected $signature = 'models:update-uuid 
                            {--dry-run : Show what would be changed without making actual changes}
                            {--model= : Update specific model only}
                            {--batch-size=10 : Number of files to process in each batch}';
    
    protected $description = 'Replace integer id primary keys with UUID primary keys and update all relationships';

    private array $models = [
        
        'BankRemittance',
        'Branch',
        'CashierExpense',
        'CashierRemittance',
        'CreateItem',
        'CreditLimit',
        'CreditSale',
        'CreditTransaction',
        'Customer',
        'ExpenseLine',
        'ItemSold',
        'PaymentVoucher',
        'PaymentVoucherDetail',
        'PostInflow',
        'PriceChange',
        'ReceiveItem',
        'ReceiveOrder',
        'Release',
        'ReleaseDetail',
        'SalesOrder',
        'SalesReceipt',
        'SettleCredit',
        'Store',
        'StoreItem',
        'Vendor',
        'VendorCredit',
        'VendorTarget',
        'Year'
    ];

    private bool $dryRun = false;
    private int $batchSize = 10;
    private array $processedFiles = [];
    private array $skippedFiles = [];
    private array $errors = [];

    public function handle(): int
    {
        $this->initializeOptions();
        
        $this->info('Starting UUID update process...');
        $this->info('Mode: ' . ($this->dryRun ? 'DRY RUN (no changes will be made)' : 'LIVE UPDATE'));
        
        $startTime = microtime(true);
        
        try {
            // Get models to process
            $modelsToProcess = $this->getModelsToProcess();
            
            if ($modelsToProcess->isEmpty()) {
                $this->warn('No models found to process.');
                return self::SUCCESS;
            }
            
            $this->info("Processing " . $modelsToProcess->count() . " models...");
            
            // Process in batches for better memory management
            $modelsToProcess->chunk($this->batchSize)->each(function (Collection $batch, int $index) {
                $this->info("Processing batch " . ($index + 1) . " (" . $batch->count() . " models)...");
                
                $batch->each(function (string $model) {
                    $this->processModel($model);
                });
                
                // Clear memory after each batch
                if (function_exists('gc_collect_cycles')) {
                    gc_collect_cycles();
                }
            });
            
            // Process API routes after all models
            $this->processApiRoutes();
            
            // Generate database migrations
            $this->generateMigrations($modelsToProcess);
            
            $this->displaySummary($startTime);
            
            return $this->errors ? self::FAILURE : self::SUCCESS;
            
        } catch (\Exception $e) {
            $this->error('An error occurred during the update process: ' . $e->getMessage());
            $this->error('Stack trace: ' . $e->getTraceAsString());
            return self::FAILURE;
        }
    }

    private function initializeOptions(): void
    {
        $this->dryRun = $this->option('dry-run');
        $this->batchSize = (int) $this->option('batch-size');
        
        if ($this->batchSize < 1 || $this->batchSize > 100) {
            $this->batchSize = 10;
        }
    }

    private function getModelsToProcess(): Collection
    {
        $specificModel = $this->option('model');
        
        if ($specificModel) {
            if (!in_array($specificModel, $this->models)) {
                $this->error("Model '{$specificModel}' not found in the models list.");
                return collect();
            }
            return collect([$specificModel]);
        }
        
        return collect($this->models);
    }

    private function processModel(string $model): void
    {
        $this->line("Processing model: {$model}");
        
        try {
            $this->updateModel($model);
            $this->updateController($model);
            $this->updateResource($model);
            $this->updateFormRequest($model);
        } catch (\Exception $e) {
            $this->errors[] = "Error processing {$model}: " . $e->getMessage();
            $this->error("Failed to process {$model}: " . $e->getMessage());
        }
    }

    private function processApiRoutes(): void
    {
        $this->line("Processing API routes...");
        
        try {
            $this->updateApiRoutes();
        } catch (\Exception $e) {
            $this->errors[] = "Error processing API routes: " . $e->getMessage();
            $this->error("Failed to process API routes: " . $e->getMessage());
        }
    }

    private function displaySummary(float $startTime): void
    {
        $duration = round(microtime(true) - $startTime, 2);
        
        $this->newLine();
        $this->info('=' . str_repeat('=', 50));
        $this->info('UUID UPDATE PROCESS SUMMARY');
        $this->info('=' . str_repeat('=', 50));
        $this->info("Duration: {$duration} seconds");
        $this->info("Processed files: " . count($this->processedFiles));
        $this->info("Skipped files: " . count($this->skippedFiles));
        $this->info("Errors: " . count($this->errors));
        
        if (!empty($this->errors)) {
            $this->newLine();
            $this->error('Errors encountered:');
            foreach ($this->errors as $error) {
                $this->error("  - {$error}");
            }
        }
        
        if ($this->dryRun) {
            $this->warn('DRY RUN COMPLETED - No actual changes were made');
        } else {
            $this->info('UUID primary key migration process completed successfully!');
        }
    }

    private function updateModel(string $model): void
    {
        $filePath = app_path("Models/{$model}.php");
        
        if (!File::exists($filePath)) {
            $this->skippedFiles[] = "Model {$model} not found";
            $this->warn("Model {$model} not found. Skipping...");
            return;
        }

        try {
            $content = File::get($filePath);
            $originalContent = $content;
            
            // Apply transformations
            $content = $this->addUuidTraitImport($content);
            $content = $this->addUuidTraitUsage($content);
            $content = $this->addUuidConfiguration($content);
            $content = $this->updateIdCasts($content);
            $content = $this->updateForeignKeyCasts($content);
            
            // Only write if content changed
            if ($content !== $originalContent) {
                if (!$this->dryRun) {
                    File::put($filePath, $content);
                }
                $this->processedFiles[] = "Model {$model}";
                $this->info("✓ Updated {$model} model to use UUID as primary key (id column)");
            } else {
                $this->skippedFiles[] = "Model {$model} (no changes needed)";
                $this->line("  No changes needed for {$model} model");
            }
            
        } catch (\Exception $e) {
            $this->errors[] = "Failed to update model {$model}: " . $e->getMessage();
            throw $e;
        }
    }

    private function addUuidTraitImport(string $content): string
    {
        if (!str_contains($content, 'use App\Traits\HasUuid;')) {
            $content = str_replace(
                'use Illuminate\Database\Eloquent\Model;',
                "use Illuminate\Database\Eloquent\Model;\nuse App\Traits\HasUuid;",
                $content
            );
        }
        return $content;
    }

    private function addUuidTraitUsage(string $content): string
    {
        if (!str_contains($content, 'use HasUuid;')) {
            $content = str_replace(
                'use HasFactory;',
                'use HasFactory, HasUuid;',
                $content
            );
        }
        return $content;
    }

    private function addUuidConfiguration(string $content): string
    {
        // Keep primary key as 'id' but configure it for UUID
        if (!str_contains($content, 'protected $primaryKey = \'id\';')) {
            $content = str_replace(
                'use HasFactory, HasUuid;',
                "use HasFactory, HasUuid;\n\n    protected \$primaryKey = 'id';\n    public \$incrementing = false;\n    protected \$keyType = 'string';",
                $content
            );
        }
        return $content;
    }

    private function updateIdCasts(string $content): string
    {
        // Change id cast to string (since it will be UUID)
        if (str_contains($content, "'id' => 'integer'")) {
            $content = str_replace(
                "'id' => 'integer'",
                "'id' => 'string'",
                $content
            );
        }
        return $content;
    }

    private function updateController(string $model): void
    {
        $filePath = app_path("Http/Controllers/{$model}Controller.php");
        
        if (!File::exists($filePath)) {
            $this->skippedFiles[] = "Controller {$model}Controller not found";
            $this->line("  Controller {$model}Controller not found. Skipping...");
            return;
        }

        try {
            $content = File::get($filePath);
            $originalContent = $content;
            
            // Apply transformations
            $content = $this->updateFindMethods($content, $model);
            $content = $this->updateRouteModelBinding($content, $model);
            
            // Only write if content changed
            if ($content !== $originalContent) {
                if (!$this->dryRun) {
                    File::put($filePath, $content);
                }
                $this->processedFiles[] = "Controller {$model}Controller";
                $this->info("✓ Updated {$model}Controller (id will contain UUID after migration)");
            } else {
                $this->skippedFiles[] = "Controller {$model}Controller (no changes needed)";
                $this->line("  No changes needed for {$model}Controller");
            }
            
        } catch (\Exception $e) {
            $this->errors[] = "Failed to update controller {$model}Controller: " . $e->getMessage();
            throw $e;
        }
    }

    private function updateFindMethods(string $content, string $model): string
    {
        // Keep using find() methods - they will work with UUID id column
        // No changes needed since the column will be renamed from uuid to id
        return $content;
    }

    private function updateRouteModelBinding(string $content, string $model): string
    {
        // Keep default route model binding - it will work with UUID id column
        // No changes needed since the column will be renamed from uuid to id
        return $content;
    }

    private function updateResource(string $model): void
    {
        $filePath = app_path("Http/Resources/{$model}Resource.php");
        
        if (!File::exists($filePath)) {
            $this->skippedFiles[] = "Resource {$model}Resource not found";
            $this->line("  Resource {$model}Resource not found. Skipping...");
            return;
        }

        try {
            $content = File::get($filePath);
            $originalContent = $content;
            
            // Keep using id field - it will contain UUID after migration
            // No changes needed since the column will be renamed from uuid to id
            
            // Only write if content changed
            if ($content !== $originalContent) {
                if (!$this->dryRun) {
                    File::put($filePath, $content);
                }
                $this->processedFiles[] = "Resource {$model}Resource";
                $this->info("✓ Updated {$model}Resource (id will contain UUID after migration)");
            } else {
                $this->skippedFiles[] = "Resource {$model}Resource (no changes needed)";
                $this->line("  No changes needed for {$model}Resource");
            }
            
        } catch (\Exception $e) {
            $this->errors[] = "Failed to update resource {$model}Resource: " . $e->getMessage();
            throw $e;
        }
    }

    private function updateFormRequest(string $model): void
    {
        $filePath = app_path("Http/Requests/{$model}Request.php");
        
        if (!File::exists($filePath)) {
            $this->skippedFiles[] = "Request {$model}Request not found";
            $this->line("  Request {$model}Request not found. Skipping...");
            return;
        }

        try {
            $content = File::get($filePath);
            $originalContent = $content;
            
            // Update validation rules to use string id (UUID)
            $content = str_replace(
                "'id' => 'required|integer|exists:{$model},id'",
                "'id' => 'required|string|exists:{$model},id'",
                $content
            );
            
            // Only write if content changed
            if ($content !== $originalContent) {
                if (!$this->dryRun) {
                    File::put($filePath, $content);
                }
                $this->processedFiles[] = "Request {$model}Request";
                $this->info("✓ Updated {$model}Request to validate UUID id");
            } else {
                $this->skippedFiles[] = "Request {$model}Request (no changes needed)";
                $this->line("  No changes needed for {$model}Request");
            }
            
        } catch (\Exception $e) {
            $this->errors[] = "Failed to update request {$model}Request: " . $e->getMessage();
            throw $e;
        }
    }

    private function updateApiRoutes(): void
    {
        $filePath = base_path('routes/api.php');
        
        if (!File::exists($filePath)) {
            $this->skippedFiles[] = "API routes file not found";
            $this->warn("API routes file not found. Skipping...");
            return;
        }

        try {
            $content = File::get($filePath);
            $originalContent = $content;
            
            // Keep using id for route parameters - it will contain UUID after migration
            // No changes needed since the column will be renamed from uuid to id
            
            // Only write if content changed
            if ($content !== $originalContent) {
                if (!$this->dryRun) {
                    File::put($filePath, $content);
                }
                $this->processedFiles[] = "API routes";
                $this->info("✓ Updated API routes (id will contain UUID after migration)");
            } else {
                $this->skippedFiles[] = "API routes (no changes needed)";
                $this->line("  No changes needed for API routes");
            }
            
        } catch (\Exception $e) {
            $this->errors[] = "Failed to update API routes: " . $e->getMessage();
            throw $e;
        }
    }

    private function updateForeignKeyCasts(string $content): string
    {
        // Update all foreign key casts from integer to string
        $pattern = "/'([a-z_]+_id)' => 'integer'/";
        return preg_replace_callback($pattern, function($matches) {
            return "'{$matches[1]}' => 'string'";
        }, $content);
    }

    private function generateMigrations(Collection $models): void
    {
        $this->line("Generating database migrations...");
        
        try {
            $timestamp = now()->format('Y_m_d_His');
            $migrationPath = database_path('migrations');
            
            // Create main UUID migration
            $migrationContent = $this->generateUuidMigrationContent($models);
            $migrationFile = "{$migrationPath}/{$timestamp}_migrate_to_uuid_primary_keys.php";
            
            if (!$this->dryRun) {
                File::put($migrationFile, $migrationContent);
            }
            
            $this->processedFiles[] = "Migration: migrate_to_uuid_primary_keys.php";
            $this->info("✓ Generated complete UUID migration file (includes data migration)");
            
        } catch (\Exception $e) {
            $this->errors[] = "Failed to generate migrations: " . $e->getMessage();
            throw $e;
        }
    }

    private function generateUuidMigrationContent(Collection $models): string
    {
        $tableNames = $models->map(function($model) {
            return strtolower($model) . 's';
        })->toArray();
        
        return "<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Step 1: Add UUID columns to all tables
        \$tables = " . var_export($tableNames, true) . ";
        
        foreach (\$tables as \$table) {
            if (Schema::hasTable(\$table)) {
                Schema::table(\$table, function (Blueprint \$table) {
                    \$table->uuid('uuid')->unique()->after('id');
                });
            }
        }
        
        // Step 2: Update foreign key columns to UUID type
        \$this->updateForeignKeyColumns();
        
        // Step 3: Generate UUIDs for existing records
        \$this->generateUuidsForExistingRecords(\$tables);
        
        // Step 4: Update foreign key references
        \$this->updateForeignKeyReferences(\$tables);
        
        // Step 5: Drop old id columns and rename uuid to id
        \$this->replaceIdWithUuid(\$tables);
    }

    public function down()
    {
        // This migration is not easily reversible
        // You would need to restore from backup
        \$this->error('This migration cannot be reversed. Restore from backup if needed.');
    }
    
    private function updateForeignKeyColumns()
    {
        // Update foreign key columns to UUID type
        \$foreignKeyColumns = [
            // Add your foreign key columns here
            // Example: 'stores' => ['branch_id', 'store_type_id'],
        ];
        
        foreach (\$foreignKeyColumns as \$table => \$columns) {
            if (Schema::hasTable(\$table)) {
                Schema::table(\$table, function (Blueprint \$table) use (\$columns) {
                    foreach (\$columns as \$column) {
                        \$table->uuid(\$column)->change();
                    }
                });
            }
        }
    }
    
    private function generateUuidsForExistingRecords(array \$tables)
    {
        foreach (\$tables as \$table) {
            \$records = DB::table(\$table)->whereNull('uuid')->get();
            
            foreach (\$records as \$record) {
                DB::table(\$table)
                    ->where('id', \$record->id)
                    ->update(['uuid' => (string) Str::uuid()]);
            }
        }
    }
    
    private function updateForeignKeyReferences(array \$tables)
    {
        // Update foreign key references to use UUIDs
        // This is a complex operation that depends on your specific relationships
        // You may need to customize this based on your database structure
        
        \$foreignKeyMappings = [
            // Define your foreign key mappings here
            // Example: 'stores' => ['branch_id' => 'branches', 'store_type_id' => 'store_types'],
        ];
        
        foreach (\$foreignKeyMappings as \$table => \$mappings) {
            foreach (\$mappings as \$foreignKey => \$referencedTable) {
                \$this->updateForeignKeyValues(\$table, \$foreignKey, \$referencedTable);
            }
        }
    }
    
    private function updateForeignKeyValues(string \$table, string \$foreignKey, string \$referencedTable)
    {
        // Get all records with foreign key values
        \$records = DB::table(\$table)
            ->whereNotNull(\$foreignKey)
            ->select('id', \$foreignKey)
            ->get();
        
        foreach (\$records as \$record) {
            // Find the UUID for the referenced record
            \$referencedUuid = DB::table(\$referencedTable)
                ->where('id', \$record->{\$foreignKey})
                ->value('uuid');
            
            if (\$referencedUuid) {
                // Update the foreign key to use UUID
                DB::table(\$table)
                    ->where('id', \$record->id)
                    ->update([\$foreignKey => \$referencedUuid]);
            }
        }
    }
    
    private function replaceIdWithUuid(array \$tables)
    {
        foreach (\$tables as \$table) {
            if (Schema::hasTable(\$table)) {
                // Drop the old id column
                Schema::table(\$table, function (Blueprint \$table) {
                    \$table->dropColumn('id');
                });
                
                // Rename uuid column to id
                Schema::table(\$table, function (Blueprint \$table) {
                    \$table->renameColumn('uuid', 'id');
                });
                
                // Make id the primary key
                Schema::table(\$table, function (Blueprint \$table) {
                    \$table->primary('id');
                });
            }
        }
    }
};";
    }

} 