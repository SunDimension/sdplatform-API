<?php

namespace App\Console;

use App\Jobs\HoldingQuantityReturnJob;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Log;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')->hourly();
        $schedule->job(new HoldingQuantityReturnJob())->dailyAt("00:01");
        
        // Data Synchronization Schedule
        if (config('sync.scheduling.enabled', true)) {
            // Run scheduled sync every 15 minutes during business hours
            $schedule->command('sync:scheduled')
                ->everyFifteenMinutes()
                ->between('8:00', '18:00')
                ->weekdays()
                ->withoutOverlapping()
                ->runInBackground()
                ->onSuccess(function () {
                    Log::info('Scheduled sync completed successfully');
                })
                ->onFailure(function () {
                    Log::error('Scheduled sync failed');
                });
            
            // Run sync health check every hour
            $schedule->command('sync:auto --health-check')
                ->hourly()
                ->withoutOverlapping()
                ->runInBackground();
                
            // Process failed items every 2 hours
            $schedule->command('sync:auto --mode=queue-only --retry-failed')
                ->everyTwoHours()
                ->withoutOverlapping()
                ->runInBackground();
        }
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
