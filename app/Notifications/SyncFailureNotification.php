<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class SyncFailureNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected array $failureData;
    protected string $locationId;
    protected string $failureType;

    public function __construct(array $failureData, string $failureType = 'general')
    {
        $this->failureData = $failureData;
        $this->locationId = config('sync.location_id', 'unknown');
        $this->failureType = $failureType;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable): MailMessage
    {
        $subject = $this->getSubject();
        $greeting = $this->getGreeting();
        $content = $this->getContent();
        $actionText = $this->getActionText();
        $actionUrl = $this->getActionUrl();

        $message = (new MailMessage)
            ->subject($subject)
            ->greeting($greeting)
            ->line($content);

        if ($actionText && $actionUrl) {
            $message->action($actionText, $actionUrl);
        }

        $message->line('Please review the synchronization system and take appropriate action.')
            ->line('This is an automated notification from your data synchronization system.');

        // Add failure details as table
        if (!empty($this->failureData)) {
            $message->line('Failure Details:');
            foreach ($this->failureData as $key => $value) {
                if (is_array($value)) {
                    $message->line("{$key}: " . json_encode($value));
                } else {
                    $message->line("{$key}: {$value}");
                }
            }
        }

        return $message;
    }

    /**
     * Get the subject line for the notification.
     */
    protected function getSubject(): string
    {
        $location = ucfirst(str_replace('_', ' ', $this->locationId));
        
        switch ($this->failureType) {
            case 'push':
                return "🚨 Push Sync Failure - {$location}";
            case 'pull':
                return "🚨 Pull Sync Failure - {$location}";
            case 'queue':
                return "🚨 Queue Processing Failure - {$location}";
            case 'health_check':
                return "🚨 Sync Health Check Failed - {$location}";
            case 'critical_items':
                return "🚨 Critical Sync Items Detected - {$location}";
            default:
                return "🚨 Synchronization Failure - {$location}";
        }
    }

    /**
     * Get the greeting for the notification.
     */
    protected function getGreeting(): string
    {
        return "Synchronization Failure Alert";
    }

    /**
     * Get the main content for the notification.
     */
    protected function getContent(): string
    {
        switch ($this->failureType) {
            case 'push':
                return "A push synchronization operation has failed. Local changes could not be sent to the central hub.";
            case 'pull':
                return "A pull synchronization operation has failed. Changes from the central hub could not be retrieved.";
            case 'queue':
                return "Queue processing has failed. Pending synchronization items could not be processed.";
            case 'health_check':
                return "A synchronization health check has failed. The system may be experiencing issues.";
            case 'critical_items':
                return "Critical synchronization items have been detected that require immediate attention.";
            default:
                return "A synchronization operation has failed and requires attention.";
        }
    }

    /**
     * Get the action text for the notification.
     */
    protected function getActionText(): ?string
    {
        return "View Sync Status";
    }

    /**
     * Get the action URL for the notification.
     */
    protected function getActionUrl(): ?string
    {
        $baseUrl = config('app.url');
        return "{$baseUrl}/api/sync/status";
    }

    /**
     * Get the array representation for storing in the database.
     */
    public function toArray($notifiable): array
    {
        return [
            'failure_type' => $this->failureType,
            'location_id' => $this->locationId,
            'failure_data' => $this->failureData,
            'timestamp' => now()->toISOString(),
            'action_url' => $this->getActionUrl(),
        ];
    }

    /**
     * Get the notification's unique identifier.
     */
    public function id(): string
    {
        return "sync_failure_{$this->locationId}_{$this->failureType}_" . now()->timestamp;
    }
}
