<?php

namespace Tests\Feature\Console;

use Tests\TestCase;
use App\Console\Commands\AutoSync;
use App\Services\SyncService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Mockery;

class AutoSyncTest extends TestCase
{
    use RefreshDatabase;

    protected $syncService;
    protected $command;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Mock the SyncService
        $this->syncService = Mockery::mock(SyncService::class);
        $this->app->instance(SyncService::class, $this->syncService);
        
        $this->command = new AutoSync($this->syncService);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /** @test */
    public function it_can_execute_full_sync_mode()
    {
        // Mock successful sync operations
        $this->syncService->shouldReceive('isOnline')->andReturn(true);
        $this->syncService->shouldReceive('pushChanges')->andReturn([
            'success' => 5,
            'failed' => 0,
            'errors' => []
        ]);
        $this->syncService->shouldReceive('pullChanges')->andReturn([
            'success' => 3,
            'failed' => 0,
            'errors' => []
        ]);
        $this->syncService->shouldReceive('processQueue')->andReturn([
            'processed' => 2,
            'failed' => 0,
            'errors' => []
        ]);

        $this->artisan('sync:auto', ['--mode' => 'full'])
            ->expectsOutput('🤖 Starting automated synchronization...')
            ->expectsOutput('🔄 Executing full synchronization...')
            ->expectsOutput('📤 Pushing local changes...')
            ->expectsOutput('📥 Pulling changes from central hub...')
            ->expectsOutput('🔄 Processing sync queue...')
            ->expectsOutput('📊 Synchronization Report')
            ->assertExitCode(0);
    }

    /** @test */
    public function it_can_execute_push_only_mode()
    {
        $this->syncService->shouldReceive('isOnline')->andReturn(true);
        $this->syncService->shouldReceive('pushChanges')->andReturn([
            'success' => 3,
            'failed' => 1,
            'errors' => [['error' => 'Connection timeout']]
        ]);

        $this->artisan('sync:auto', ['--mode' => 'push-only'])
            ->expectsOutput('🤖 Starting automated synchronization...')
            ->expectsOutput('📤 Pushing local changes...')
            ->expectsOutput('✅ Push completed: 3 success, 1 failed')
            ->assertExitCode(0);
    }

    /** @test */
    public function it_can_execute_pull_only_mode()
    {
        $this->syncService->shouldReceive('isOnline')->andReturn(true);
        $this->syncService->shouldReceive('pullChanges')->andReturn([
            'success' => 2,
            'failed' => 0,
            'errors' => []
        ]);

        $this->artisan('sync:auto', ['--mode' => 'pull-only'])
            ->expectsOutput('🤖 Starting automated synchronization...')
            ->expectsOutput('📥 Pulling changes from central hub...')
            ->expectsOutput('✅ Pull completed: 2 success, 0 failed')
            ->assertExitCode(0);
    }

    /** @test */
    public function it_can_execute_queue_only_mode()
    {
        $this->syncService->shouldReceive('isOnline')->andReturn(true);
        $this->syncService->shouldReceive('processQueue')->andReturn([
            'processed' => 4,
            'failed' => 1,
            'errors' => [['item_id' => 123, 'error' => 'Validation failed']]
        ]);

        $this->artisan('sync:auto', ['--mode' => 'queue-only'])
            ->expectsOutput('🤖 Starting automated synchronization...')
            ->expectsOutput('🔄 Processing sync queue...')
            ->assertExitCode(0);
    }

    /** @test */
    public function it_handles_offline_status_gracefully()
    {
        $this->syncService->shouldReceive('isOnline')->andReturn(false);
        $this->syncService->shouldReceive('processQueue')->andReturn([
            'processed' => 1,
            'failed' => 0,
            'errors' => []
        ]);

        $this->artisan('sync:auto', ['--mode' => 'full'])
            ->expectsOutput('🤖 Starting automated synchronization...')
            ->expectsOutput('⚠️  System appears offline. Processing queue only...')
            ->expectsOutput('🔄 Processing sync queue...')
            ->assertExitCode(0);
    }

    /** @test */
    public function it_can_force_sync_when_offline()
    {
        $this->syncService->shouldReceive('isOnline')->andReturn(false);
        $this->syncService->shouldReceive('pushChanges')->andReturn([
            'success' => 2,
            'failed' => 0,
            'errors' => []
        ]);
        $this->syncService->shouldReceive('pullChanges')->andReturn([
            'success' => 1,
            'failed' => 0,
            'errors' => []
        ]);
        $this->syncService->shouldReceive('processQueue')->andReturn([
            'processed' => 0,
            'failed' => 0,
            'errors' => []
        ]);

        $this->artisan('sync:auto', ['--mode' => 'full', '--force' => true])
            ->expectsOutput('🤖 Starting automated synchronization...')
            ->expectsOutput('🔄 Executing full synchronization...')
            ->assertExitCode(0);
    }

    /** @test */
    public function it_can_perform_health_check()
    {
        $this->syncService->shouldReceive('isOnline')->andReturn(true);
        $this->syncService->shouldReceive('processQueue')->andReturn([
            'processed' => 0,
            'failed' => 0,
            'errors' => []
        ]);

        $this->artisan('sync:auto', ['--mode' => 'queue-only', '--health-check' => true])
            ->expectsOutput('🤖 Starting automated synchronization...')
            ->expectsOutput('🏥 Performing health check...')
            ->assertExitCode(0);
    }

    /** @test */
    public function it_can_retry_failed_items()
    {
        $this->syncService->shouldReceive('isOnline')->andReturn(true);
        $this->syncService->shouldReceive('processQueue')->andReturn([
            'processed' => 0,
            'failed' => 0,
            'errors' => []
        ]);

        $this->artisan('sync:auto', ['--mode' => 'queue-only', '--retry-failed' => true])
            ->expectsOutput('🤖 Starting automated synchronization...')
            ->expectsOutput('🔄 Processing sync queue...')
            ->expectsOutput('🔄 Retrying failed items...')
            ->assertExitCode(0);
    }

    /** @test */
    public function it_handles_sync_service_exceptions()
    {
        $this->syncService->shouldReceive('isOnline')->andReturn(true);
        $this->syncService->shouldReceive('pushChanges')->andThrow(new \Exception('Service unavailable'));

        $this->artisan('sync:auto', ['--mode' => 'push-only'])
            ->expectsOutput('🤖 Starting automated synchronization...')
            ->expectsOutput('📤 Pushing local changes...')
            ->expectsOutput('❌ Push synchronization failed: Service unavailable')
            ->expectsOutput('❌ Automated synchronization failed: Service unavailable')
            ->assertExitCode(1);
    }

    /** @test */
    public function it_shows_detailed_output_when_requested()
    {
        $this->syncService->shouldReceive('isOnline')->andReturn(true);
        $this->syncService->shouldReceive('pushChanges')->andReturn([
            'success' => 2,
            'failed' => 1,
            'errors' => [['error' => 'Network error']]
        ]);

        $this->artisan('sync:auto', ['--mode' => 'push-only', '--detailed' => true])
            ->expectsOutput('🤖 Starting automated synchronization...')
            ->expectsOutput('📤 Pushing local changes...')
            ->expectsOutput('⚠️  Push errors encountered:')
            ->expectsOutput('   - Network error')
            ->assertExitCode(0);
    }

    /** @test */
    public function it_caches_sync_statistics()
    {
        $this->syncService->shouldReceive('isOnline')->andReturn(true);
        $this->syncService->shouldReceive('processQueue')->andReturn([
            'processed' => 1,
            'failed' => 0,
            'errors' => []
        ]);

        $this->artisan('sync:auto', ['--mode' => 'queue-only']);

        // Verify that stats were cached
        $this->assertTrue(Cache::has('sync_stats_' . now()->format('Y-m-d_H-i-s')));
    }

    /** @test */
    public function it_logs_sync_completion()
    {
        Log::shouldReceive('info')->once()->with('AutoSync completed', Mockery::any());

        $this->syncService->shouldReceive('isOnline')->andReturn(true);
        $this->syncService->shouldReceive('processQueue')->andReturn([
            'processed' => 1,
            'failed' => 0,
            'errors' => []
        ]);

        $this->artisan('sync:auto', ['--mode' => 'queue-only'])
            ->assertExitCode(0);
    }
}
