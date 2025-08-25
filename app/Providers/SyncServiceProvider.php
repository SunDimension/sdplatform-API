<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\SyncService;
use App\Console\Commands\AutoSync;
use Illuminate\Console\Application as Artisan;
use Illuminate\Console\Scheduling\Schedule;

class SyncServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../../config/sync.php', 'sync');

        $this->app->singleton(SyncService::class, function ($app) {
            return new SyncService(
                config('sync.batch_size', 100),
                config('sync.max_memory_mb', 512),
                config('sync.retry_attempts', 3)
            );
        });

        // Register the AutoSync command
        $this->app->singleton(AutoSync::class, function ($app) {
            return new AutoSync($app->make(SyncService::class));
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Publish configuration file
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../../config/sync.php' => config_path('sync.php'),
            ], 'sync-config');

            // Register commands
            $this->commands([
                AutoSync::class,
            ]);

            // Schedule automated sync if enabled
            if (config('sync.scheduling.enabled', false)) {
                $this->scheduleSync();
            }
        }

        // Register sync middleware if needed
        $this->registerMiddleware();
    }

    /**
     * Schedule automated synchronization
     */
    protected function scheduleSync(): void
    {
        $schedule = $this->app->make(Schedule::class);
        
        $syncInterval = config('sync.scheduling.interval_minutes', 15);
        $syncMode = config('sync.scheduling.default_mode', 'full');
        
        $schedule->command("sync:auto --mode={$syncMode}")
            ->everyMinute()
            ->when(function () {
                return config('sync.scheduling.enabled', false);
            })
            ->withoutOverlapping()
            ->runInBackground()
            ->appendOutputTo(storage_path('logs/sync-scheduled.log'));
    }

    /**
     * Register sync middleware
     */
    protected function registerMiddleware(): void
    {
        // Register any sync-related middleware here
        // For example, rate limiting, authentication, etc.
    }

    /**
     * Get the services provided by the provider.
     */
    public function provides(): array
    {
        return [
            SyncService::class,
            AutoSync::class,
        ];
    }
}
