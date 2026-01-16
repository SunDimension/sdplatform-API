<?php

namespace App\Services;

use App\Notifications\SyncFailureNotification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class SyncNotificationService
{
    protected array $config;
    protected string $locationId;

    public function __construct()
    {
        $this->config = config('sync.notifications', []);
        $this->locationId = config('sync.location_id', 'unknown');
    }

    /**
     * Send notification for sync failure
     */
    public function notifySyncFailure(array $failureData, string $failureType = 'general'): void
    {
        if (!$this->shouldSendNotification('sync_failed')) {
            Log::info('Sync failure notification skipped - notifications disabled');
            return;
        }

        try {
            // Check if we've already sent a notification for this failure recently
            if ($this->hasRecentNotification($failureType, $failureData)) {
                Log::info("Recent notification already sent for {$failureType} failure");
                return;
            }

            // Get notification recipients
            $recipients = $this->getNotificationRecipients();
            
            if (empty($recipients)) {
                Log::warning('No notification recipients configured for sync failures');
                return;
            }

            // Create and send notification
            $notification = new SyncFailureNotification($failureData, $failureType);
            
            foreach ($recipients as $recipient) {
                Notification::route('mail', $recipient)->notify($notification);
            }

            // Log the notification
            $this->logNotificationSent($failureType, $failureData);
            
            Log::info("Sync failure notification sent for {$failureType} to " . count($recipients) . " recipients");

        } catch (\Exception $e) {
            Log::error('Failed to send sync failure notification: ' . $e->getMessage(), [
                'failure_type' => $failureType,
                'failure_data' => $failureData,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Send notification for critical items detected
     */
    public function notifyCriticalItems(array $criticalItems): void
    {
        if (!$this->shouldSendNotification('critical_items')) {
            return;
        }

        $failureData = [
            'critical_items_count' => count($criticalItems),
            'items' => $criticalItems,
            'detected_at' => now()->toISOString(),
            'priority_threshold' => 8
        ];

        $this->notifySyncFailure($failureData, 'critical_items');
    }

    /**
     * Send notification for health check failure
     */
    public function notifyHealthCheckFailure(array $healthData): void
    {
        if (!$this->shouldSendNotification('health_check_failed')) {
            return;
        }

        $failureData = [
            'health_check_failed_at' => now()->toISOString(),
            'health_data' => $healthData,
            'system_status' => $healthData['status'] ?? 'unknown'
        ];

        $this->notifySyncFailure($failureData, 'health_check');
    }

    /**
     * Send notification for high failure rate
     */
    public function notifyHighFailureRate(float $failureRate, array $failureDetails): void
    {
        if (!$this->shouldSendNotification('high_failure_rate')) {
            return;
        }

        $failureData = [
            'failure_rate' => $failureRate,
            'failure_details' => $failureDetails,
            'threshold' => config('sync.monitoring.performance_thresholds.max_failure_rate', 0.1),
            'detected_at' => now()->toISOString()
        ];

        $this->notifySyncFailure($failureData, 'high_failure_rate');
    }

    /**
     * Check if notification should be sent
     */
    protected function shouldSendNotification(string $eventType): bool
    {
        if (!$this->config['enabled']) {
            return false;
        }

        return $this->config['events'][$eventType] ?? false;
    }

    /**
     * Get notification recipients
     */
    protected function getNotificationRecipients(): array
    {
        $recipients = $this->config['recipients'] ?? [];
        
        // If no recipients configured, try to get from environment
        if (empty($recipients)) {
            $envRecipients = env('SYNC_NOTIFICATION_RECIPIENTS');
            if ($envRecipients) {
                $recipients = array_map('trim', explode(',', $envRecipients));
            }
        }

        // Filter out empty values
        return array_filter($recipients);
    }

    /**
     * Check if we've sent a recent notification for this failure
     */
    protected function hasRecentNotification(string $failureType, array $failureData): bool
    {
        $cacheKey = "sync_notification_{$this->locationId}_{$failureType}_" . md5(json_encode($failureData));
        $recentNotification = Cache::get($cacheKey);
        
        if ($recentNotification) {
            $lastSent = Carbon::parse($recentNotification);
            $cooldownMinutes = $this->getNotificationCooldown($failureType);
            
            return $lastSent->addMinutes($cooldownMinutes)->isFuture();
        }
        
        return false;
    }

    /**
     * Log that a notification was sent
     */
    protected function logNotificationSent(string $failureType, array $failureData): void
    {
        $cacheKey = "sync_notification_{$this->locationId}_{$failureType}_" . md5(json_encode($failureData));
        $cacheTtl = $this->getNotificationCooldown($failureType) * 60; // Convert to seconds
        
        Cache::put($cacheKey, now()->toISOString(), $cacheTtl);
    }

    /**
     * Get notification cooldown period in minutes
     */
    protected function getNotificationCooldown(string $failureType): int
    {
        $cooldowns = [
            'push' => 30,           // 30 minutes for push failures
            'pull' => 60,           // 1 hour for pull failures
            'queue' => 45,          // 45 minutes for queue failures
            'health_check' => 120,  // 2 hours for health check failures
            'critical_items' => 15, // 15 minutes for critical items
            'high_failure_rate' => 180, // 3 hours for high failure rates
            'general' => 60         // 1 hour for general failures
        ];
        
        return $cooldowns[$failureType] ?? 60;
    }

    /**
     * Send test notification
     */
    public function sendTestNotification(string $recipient): bool
    {
        try {
            $testData = [
                'test_notification' => true,
                'sent_at' => now()->toISOString(),
                'location_id' => $this->locationId,
                'message' => 'This is a test notification to verify the sync notification system is working correctly.'
            ];
            
            $notification = new SyncFailureNotification($testData, 'test');
            Notification::route('mail', $recipient)->notify($notification);
            
            Log::info("Test notification sent successfully to {$recipient}");
            return true;
            
        } catch (\Exception $e) {
            Log::error('Failed to send test notification: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get notification statistics
     */
    public function getNotificationStats(): array
    {
        $stats = [];
        $locationId = $this->locationId;
        
        // Get notification counts for different types
        $notificationTypes = ['push', 'pull', 'queue', 'health_check', 'critical_items', 'high_failure_rate'];
        
        foreach ($notificationTypes as $type) {
            $cacheKey = "sync_notification_{$locationId}_{$type}_count";
            $stats[$type] = Cache::get($cacheKey, 0);
        }
        
        // Get last notification sent
        $lastNotification = Cache::get("sync_last_notification_{$locationId}");
        $stats['last_notification'] = $lastNotification ? Carbon::parse($lastNotification)->diffForHumans() : 'Never';
        
        // Get notification status
        $stats['enabled'] = $this->config['enabled'];
        $stats['recipients_count'] = count($this->getNotificationRecipients());
        
        return $stats;
    }
}
