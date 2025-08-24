<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\SyncNotificationService;

class TestSyncNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:test-notification 
                            {email : Email address to send test notification to}
                            {--type=test : Type of notification to test (test, push, pull, queue, health_check, critical_items)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test the synchronization notification system';

    protected SyncNotificationService $notificationService;

    public function __construct(SyncNotificationService $notificationService)
    {
        parent::__construct();
        $this->notificationService = $notificationService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        $type = $this->option('type');

        $this->info('🧪 Testing synchronization notification system...');
        $this->info("📧 Sending test notification to: {$email}");
        $this->info("📋 Notification type: {$type}");

        try {
            switch ($type) {
                case 'test':
                    $success = $this->notificationService->sendTestNotification($email);
                    break;
                    
                case 'push':
                    $this->notificationService->notifySyncFailure([
                        'test' => true,
                        'operation' => 'push',
                        'error' => 'Test push failure notification',
                        'timestamp' => now()->toISOString()
                    ], 'push');
                    $success = true;
                    break;
                    
                case 'pull':
                    $this->notificationService->notifySyncFailure([
                        'test' => true,
                        'operation' => 'pull',
                        'error' => 'Test pull failure notification',
                        'timestamp' => now()->toISOString()
                    ], 'pull');
                    $success = true;
                    break;
                    
                case 'queue':
                    $this->notificationService->notifySyncFailure([
                        'test' => true,
                        'operation' => 'queue',
                        'error' => 'Test queue failure notification',
                        'timestamp' => now()->toISOString()
                    ], 'queue');
                    $success = true;
                    break;
                    
                case 'health_check':
                    $this->notificationService->notifyHealthCheckFailure([
                        'test' => true,
                        'status' => 'failed',
                        'error' => 'Test health check failure notification',
                        'timestamp' => now()->toISOString()
                    ]);
                    $success = true;
                    break;
                    
                case 'critical_items':
                    $this->notificationService->notifyCriticalItems([
                        [
                            'id' => 'test-001',
                            'priority' => 9,
                            'type' => 'JournalEntry',
                            'description' => 'Test critical item notification'
                        ]
                    ]);
                    $success = true;
                    break;
                    
                default:
                    $this->error("❌ Unknown notification type: {$type}");
                    return 1;
            }

            if ($success) {
                $this->info('✅ Test notification sent successfully!');
                $this->info('📧 Check your email inbox for the notification.');
                
                // Show notification stats
                $this->showNotificationStats();
                
                return 0;
            } else {
                $this->error('❌ Failed to send test notification');
                return 1;
            }

        } catch (\Exception $e) {
            $this->error('❌ Error testing notification: ' . $e->getMessage());
            return 1;
        }
    }

    /**
     * Show notification statistics
     */
    protected function showNotificationStats(): void
    {
        $this->info('');
        $this->info('📊 Notification System Statistics');
        $this->info('===============================');
        
        try {
            $stats = $this->notificationService->getNotificationStats();
            
            $this->table(
                ['Metric', 'Value'],
                [
                    ['System Enabled', $stats['enabled'] ? '✅ Yes' : '❌ No'],
                    ['Recipients Count', $stats['recipients_count']],
                    ['Last Notification', $stats['last_notification']],
                    ['Push Notifications', $stats['push'] ?? 0],
                    ['Pull Notifications', $stats['pull'] ?? 0],
                    ['Queue Notifications', $stats['queue'] ?? 0],
                    ['Health Check Notifications', $stats['health_check'] ?? 0],
                    ['Critical Items Notifications', $stats['critical_items'] ?? 0],
                    ['High Failure Rate Notifications', $stats['high_failure_rate'] ?? 0],
                ]
            );
            
        } catch (\Exception $e) {
            $this->warn('⚠️  Could not retrieve notification statistics: ' . $e->getMessage());
        }
    }
}
