<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Artisan;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class UpdateCodeFromRemote extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'code:pull 
                            {--branch=main : The branch to pull from}
                            {--no-migrate : Skip running migrations}
                            {--no-cache : Skip clearing cache}
                            {--dry-run : Show what would be done without executing}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Pull code updates from remote repository and apply them locally';

    protected $logFile;

    public function __construct()
    {
        parent::__construct();
        $this->logFile = storage_path('logs/code-pull-' . date('Y-m-d') . '.log');
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 Starting code pull process...');
        $this->log('Starting code pull process');

        // Check if this is a central hub
        if (config('sync.is_central_hub', false)) {
            $this->error('❌ This is the central hub. Code pulls should not be run here.');
            return 1;
        }

        $dryRun = $this->option('dry-run');
        $branch = $this->option('branch');

        if ($dryRun) {
            $this->warn('🔍 DRY RUN MODE - No changes will be made');
        }

        try {
            // Step 1: Pre-update checks
            $this->info('📋 Running pre-update checks...');
            if (!$this->preUpdateChecks()) {
                return 1;
            }

            // Step 2: Backup current state
            $this->info('💾 Creating backup...');
            if (!$dryRun && !$this->createBackup()) {
                $this->error('❌ Backup failed. Aborting update.');
                return 1;
            }

            // Step 3: Put application in maintenance mode
            $this->info('🔒 Putting application in maintenance mode...');
            if (!$dryRun) {
                Artisan::call('down', ['--retry' => 60]);
            }

            // Step 4: Stash local changes
            $this->info('📦 Stashing local changes...');
            if (!$dryRun && !$this->stashLocalChanges()) {
                $this->error('❌ Failed to stash local changes.');
                $this->revertChanges();
                return 1;
            }

            // Step 5: Pull code from remote
            $this->info("🔄 Pulling code from branch: {$branch}...");
            if (!$dryRun && !$this->pullCode($branch)) {
                $this->error('❌ Failed to pull code from remote.');
                $this->revertChanges();
                return 1;
            }

            // Step 6: Install/update dependencies
            $this->info('📦 Updating Composer dependencies...');
            if (!$dryRun && !$this->updateDependencies()) {
                $this->error('❌ Failed to update dependencies.');
                $this->revertChanges();
                return 1;
            }

            // Step 7: Run migrations
            if (!$this->option('no-migrate')) {
                $this->info('🗄️ Running database migrations...');
                if (!$dryRun && !$this->runMigrations()) {
                    $this->error('❌ Migration failed.');
                    $this->revertChanges();
                    return 1;
                }
            }

            // Step 8: Clear caches
            if (!$this->option('no-cache')) {
                $this->info('🧹 Clearing application caches...');
                if (!$dryRun) {
                    $this->clearCaches();
                }
            }

            // Step 9: Bring application back up
            $this->info('🔓 Bringing application back online...');
            if (!$dryRun) {
                Artisan::call('up');
            }

            $this->info('✅ Code pull completed successfully!');
            $this->log('Code pull completed successfully');

            // Display update summary
            $this->displayUpdateSummary();

            return 0;

        } catch (\Exception $e) {
            $this->error('❌ Error during code pull: ' . $e->getMessage());
            $this->log('Error: ' . $e->getMessage());
            $this->revertChanges();
            return 1;
        }
    }

    /**
     * Run pre-update checks
     */
    protected function preUpdateChecks(): bool
    {
        // Check if Git is installed
        if (!$this->commandExists('git')) {
            $this->error('Git is not installed on this system.');
            return false;
        }

        // Check if we're in a Git repository
        if (!is_dir(base_path('.git'))) {
            $this->error('This directory is not a Git repository.');
            return false;
        }

        // Check if Composer is installed
        if (!$this->commandExists('composer')) {
            $this->error('Composer is not installed on this system.');
            return false;
        }

        // Check disk space (require at least 500MB free)
        $freeSpace = disk_free_space(base_path());
        if ($freeSpace < 500 * 1024 * 1024) {
            $this->error('Insufficient disk space. At least 500MB required.');
            return false;
        }

        // Check if there are uncommitted changes that might conflict
        $status = $this->runProcess(['git', 'status', '--porcelain']);
        if (!empty($status)) {
            $this->warn('⚠️  You have uncommitted changes:');
            $this->line($status);
            if (!$this->confirm('Do you want to continue? These changes will be stashed.')) {
                return false;
            }
        }

        $this->info('✓ All pre-update checks passed');
        return true;
    }

    /**
     * Create a backup of the current state
     */
    protected function createBackup(): bool
    {
        try {
            $backupDir = storage_path('backups/code');
            if (!is_dir($backupDir)) {
                mkdir($backupDir, 0755, true);
            }

            $timestamp = date('Y-m-d_H-i-s');
            $backupFile = $backupDir . "/backup_{$timestamp}.txt";

            // Get current Git commit hash
            $currentCommit = trim($this->runProcess(['git', 'rev-parse', 'HEAD']));
            
            // Save backup information
            file_put_contents($backupFile, "Backup created at: " . date('Y-m-d H:i:s') . "\n");
            file_put_contents($backupFile, "Git commit: {$currentCommit}\n", FILE_APPEND);
            
            $this->log("Backup created: {$backupFile}");
            return true;

        } catch (\Exception $e) {
            $this->error('Backup failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Stash local changes
     */
    protected function stashLocalChanges(): bool
    {
        try {
            $output = $this->runProcess(['git', 'stash', 'save', 'Auto-stash before code pull']);
            $this->log("Stashed local changes: {$output}");
            return true;
        } catch (\Exception $e) {
            $this->error('Failed to stash changes: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Pull code from remote repository
     */
    protected function pullCode(string $branch): bool
    {
        try {
            // Fetch latest changes
            $this->info('  Fetching latest changes...');
            $this->runProcess(['git', 'fetch', 'origin']);

            // Pull the specified branch
            $this->info("  Pulling branch: {$branch}...");
            $output = $this->runProcess(['git', 'pull', 'origin', $branch]);
            
            $this->log("Git pull output: {$output}");
            return true;

        } catch (\Exception $e) {
            $this->error('Git pull failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Update Composer dependencies
     */
    protected function updateDependencies(): bool
    {
        try {
            $this->info('  Installing/updating packages...');
            $output = $this->runProcess([
                'composer',
                'install',
                '--no-dev',
                '--optimize-autoloader',
                '--no-interaction'
            ], timeout: 300);
            
            $this->log("Composer output: {$output}");
            return true;

        } catch (\Exception $e) {
            $this->error('Composer update failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Run database migrations
     */
    protected function runMigrations(): bool
    {
        try {
            Artisan::call('migrate', ['--force' => true]);
            $output = Artisan::output();
            $this->log("Migration output: {$output}");
            $this->line($output);
            return true;

        } catch (\Exception $e) {
            $this->error('Migration failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Clear application caches
     */
    protected function clearCaches(): void
    {
        $this->info('  Clearing config cache...');
        Artisan::call('config:clear');
        
        $this->info('  Clearing route cache...');
        Artisan::call('route:clear');
        
        $this->info('  Clearing view cache...');
        Artisan::call('view:clear');
        
        $this->info('  Optimizing...');
        Artisan::call('optimize');
    }

    /**
     * Revert changes in case of failure
     */
    protected function revertChanges(): void
    {
        $this->warn('🔄 Reverting changes...');
        
        try {
            // Try to restore stashed changes
            $this->runProcess(['git', 'stash', 'pop']);
            
            // Bring application back up if it was down
            Artisan::call('up');
            
            $this->info('✓ Changes reverted');
            
        } catch (\Exception $e) {
            $this->error('Failed to revert changes: ' . $e->getMessage());
            $this->error('Manual intervention may be required!');
        }
    }

    /**
     * Display update summary
     */
    protected function displayUpdateSummary(): void
    {
        try {
            // Get current commit info
            $commit = trim($this->runProcess(['git', 'log', '-1', '--oneline']));
            $branch = trim($this->runProcess(['git', 'rev-parse', '--abbrev-ref', 'HEAD']));
            
            $this->newLine();
            $this->info('📊 Update Summary:');
            $this->table(
                ['Property', 'Value'],
                [
                    ['Current Branch', $branch],
                    ['Latest Commit', $commit],
                    ['Update Time', now()->toDateTimeString()],
                    ['Location ID', config('app.location_id', 'unknown')],
                ]
            );
            
        } catch (\Exception $e) {
            $this->warn('Could not generate update summary.');
        }
    }

    /**
     * Run a shell command and return output
     */
    protected function runProcess(array $command, int $timeout = 60): string
    {
        $process = new Process($command, base_path());
        $process->setTimeout($timeout);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        return $process->getOutput();
    }

    /**
     * Check if a command exists in the system
     */
    protected function commandExists(string $command): bool
    {
        $test = shell_exec(sprintf("which %s 2>/dev/null", escapeshellarg($command)));
        return !empty($test);
    }

    /**
     * Log message to file
     */
    protected function log(string $message): void
    {
        $timestamp = date('Y-m-d H:i:s');
        $logMessage = "[{$timestamp}] {$message}\n";
        file_put_contents($this->logFile, $logMessage, FILE_APPEND);
    }
}