<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Log;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    //   protected function schedule(Schedule $schedule)
    // {
    //     if (! config('sync.scheduling.enabled', true)) {
    //         return;
    //     }

    //     // Run sync every 15 minutes
    //     $schedule->command('sync:scheduled')
    //         ->everyThreeMinutes()
    //         ->withoutOverlapping()
    //         ->runInBackground()
    //         ->appendOutputTo(storage_path('logs/sync-scheduled.log'));

    //     // Health check every hour
    //     $schedule->command('sync:auto --health-check')
    //         ->hourly()
    //         ->withoutOverlapping()
    //         ->runInBackground()
    //         ->appendOutputTo(storage_path('logs/sync-health.log'));

    //     // Retry failed items every 2 hours
    //     $schedule->command('sync:auto --retry-failed')
    //         ->everyTwoHours()
    //         ->withoutOverlapping()
    //         ->runInBackground()
    //         ->appendOutputTo(storage_path('logs/sync-retry.log'));
    // }

    // protected function schedule(Schedule $schedule)
    // {
    //     if (! config('sync.scheduling.enabled', true)) {
    //         return;
    //     }

    //     // Critical models - sync every 15 minutes
    //     $criticalModels = [
    //         'App\Models\SalesOrder',
    //         'App\Models\SalesReceipt',
    //         'App\Models\ItemSold',
    //     ];

    //     foreach ($criticalModels as $index => $model) {
    //         $schedule->command("sync:data --push --model=\"{$model}\"")
    //             ->everyThreeMinutes()
    //             ->withoutOverlapping()
    //             ->runInBackground()
    //             ->appendOutputTo(storage_path('logs/sync-scheduled.log'));
    //     }

    //     // Less critical models - sync every 30 minutes
    //     $standardModels = [
    //         'App\Models\Customer',
    //         'App\Models\Vendor',
    //         'App\Models\CreateItem',
    //         // ... add others
    //     ];

    //     foreach ($standardModels as $model) {
    //         $schedule->command("sync:data --push --model=\"{$model}\"")
    //             ->everyThirtyMinutes()
    //             ->withoutOverlapping()
    //             ->runInBackground()
    //             ->appendOutputTo(storage_path('logs/sync-scheduled.log'));
    //     }

    //     // Health check
    //     $schedule->command('sync:auto --health-check')
    //         ->hourly()
    //         ->withoutOverlapping()
    //         ->runInBackground()
    //         ->appendOutputTo(storage_path('logs/sync-health.log'));
    // }

    protected function schedule(Schedule $schedule)
    {

        Log::info('Laravel scheduler ticked', [
            'time' => now()->toDateTimeString(),
            'user' => get_current_user(),
        ]);

        if (! config('sync.scheduling.enabled', true)) {
            return;
        }

        /*
    |--------------------------------------------------------------------------
    | TIER 1: CRITICAL TRANSACTIONAL DATA (Every 15 minutes)
    |--------------------------------------------------------------------------
    | High-volume, frequently changing data that needs near real-time sync
    | Limit: 5-8 models maximum
    */
        $tier1CriticalModels = [
            'App\Models\SalesOrder',
            'App\Models\SalesReceipt',
            'App\Models\ItemSold',
            // 'App\Models\Release',
            // 'App\Models\ReleaseDetail',
            'App\Models\ProductAudit',
            'App\Models\Transaction',
            'App\Models\JournalEntry',
            'App\Models\LedgerPosting',
            'App\Models\JournalLine',
            // 'App\Models\LedgerPostingLine',

            // Add 2-3 more if needed
        ];

        foreach ($tier1CriticalModels as $model) {
            $schedule->command("sync:data --push --model=\"{$model}\"")
                ->everyFifteenMinutes()
                // ->withoutOverlapping(10) // Lock expires after 10 minutes
                ->runInBackground()
                ->appendOutputTo(storage_path('logs/sync-critical.log'));
        }

        /*
    |--------------------------------------------------------------------------
    | TIER 2: IMPORTANT OPERATIONAL DATA (Every 30 minutes)
    |--------------------------------------------------------------------------
    | Moderate frequency changes, important but not critical
    | Limit: 10-15 models
    */
        $tier2ImportantModels = [
            'App\Models\Customer',
            'App\Models\Vendor',
            'App\Models\Supplier',
            'App\Models\CreateItem',
            'App\Models\StoreItem',
            'App\Models\ReceiveOrder',
            'App\Models\ReceiveItem',
            'App\Models\StoreTransferOrder',
            'App\Models\StoreTransferItem',
            'App\Models\PurchaseOrder',
            'App\Models\PurchaseOrderItem',
            'App\Models\GoodsRecieved',
            'App\Models\GoodsRecievedItem',

            // Can add 2-3 more
        ];

        foreach ($tier2ImportantModels as $model) {
            $schedule->command("sync:data --push --model=\"{$model}\"")
                ->everyThirtyMinutes()
                // ->withoutOverlapping(20)
                ->runInBackground()
                ->appendOutputTo(storage_path('logs/sync-important.log'));
        }

        /*
    |--------------------------------------------------------------------------
    | TIER 3: STANDARD OPERATIONAL DATA (Every Hour)
    |--------------------------------------------------------------------------
    | Lower frequency changes, still needs regular sync
    | Limit: 15-20 models
    */
        $tier3StandardModels = [
            'App\Models\PostOutflow',
            'App\Models\PostInflow',
            'App\Models\CashierRemittance',
            'App\Models\BankRemittance',
            'App\Models\ReturnItem',
            'App\Models\ReturnDetail',
            'App\Models\PriceChange',
            'App\Models\CashierExpense',
            'App\Models\CreditTransaction',
            'App\Models\SupplierPayment',
            'App\Models\SupplierInvoice',
            'App\Models\SupplierInvoiceItem',
            'App\Models\StockDisbursement',
            'App\Models\StockDisbursementItem',
            'App\Models\StockMovement',
            'App\Models\PurchaseItemCost',

            // Can add more
        ];

        foreach ($tier3StandardModels as $model) {
            $schedule->command("sync:data --push --model=\"{$model}\"")
                ->hourly()
                // ->withoutOverlapping(45)
                ->runInBackground()
                ->appendOutputTo(storage_path('logs/sync-standard.log'));
        }

        /*
    |--------------------------------------------------------------------------
    | TIER 4: CONFIGURATION/MASTER DATA (Every 2-4 Hours)
    |--------------------------------------------------------------------------
    | Rarely changes, low priority
    | Limit: 20-30 models
    */
        $tier4MasterDataModels = [
            'App\Models\User',
            'App\Models\Branch',
            'App\Models\Store',
            'App\Models\TransactionType',
            'App\Models\Bank',
            'App\Models\PaymentMethod',
            'App\Models\AccountType',

            // Add other rarely-changing models
        ];

        foreach ($tier4MasterDataModels as $model) {
            $schedule->command("sync:data --push --model=\"{$model}\"")
                ->everyTwoHours()
                // ->withoutOverlapping(90)
                ->runInBackground()
                ->appendOutputTo(storage_path('logs/sync-master.log'));
        }

        /*
    |--------------------------------------------------------------------------
    | MAINTENANCE TASKS
    |--------------------------------------------------------------------------
    */

        // Health check
        $schedule->command('sync:auto --health-check')
            ->hourly()
            ->withoutOverlapping()
            ->runInBackground()
            ->appendOutputTo(storage_path('logs/sync-health.log'));

        // Retry failed syncs
        $schedule->command('sync:auto --retry-failed')
            ->everyTwoHours()
            ->withoutOverlapping()
            ->runInBackground()
            ->appendOutputTo(storage_path('logs/sync-retry.log'));

        // Clean up old sync logs (optional)
        $schedule->command('sync:cleanup-logs')
            ->daily()
            ->at('02:00')
            ->appendOutputTo(storage_path('logs/sync-maintenance.log'));
    }
    /**
     * Register the commands for the application.
     */

    protected $commands = [
        \App\Console\Commands\DeployRollback::class,
    ];
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
