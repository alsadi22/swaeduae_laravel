<?php

namespace App\Services;

use App\Models\WebhookSubscription;
use App\Models\WebhookDeliveryLog;
use Illuminate\Support\Facades\Http;

class WebhookService
{
    /**
     * Register webhook
     */
    public function registerWebhook($url, $eventType, $filters = [], $description = null)
    {
        try {
            $webhook = WebhookSubscription::create([
                'webhook_url' => $url,
                'event_type' => $eventType,
                'is_active' => true,
                'filters' => $filters,
                'description' => $description,
            ]);

            return ['success' => true, 'webhook' => $webhook];
        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Unregister webhook
     */
    public function unregisterWebhook($webhookId)
    {
        $webhook = WebhookSubscription::findOrFail($webhookId);
        $webhook->update(['is_active' => false]);

        return ['success' => true];
    }

    /**
     * Dispatch webhook event
     */
    public function dispatchEvent($eventType, $payload)
    {
        $webhooks = WebhookSubscription::where('event_type', $eventType)
            ->where('is_active', true)
            ->get();

        foreach ($webhooks as $webhook) {
            $this->deliverWebhook($webhook, $eventType, $payload);
        }
    }

    /**
     * Deliver webhook
     */
    private function deliverWebhook($webhook, $eventType, $payload)
    {
        try {
            $response = Http::timeout(30)->post($webhook->webhook_url, [
                'event' => $eventType,
                'payload' => $payload,
                'timestamp' => now()->timestamp,
            ]);

            $log = WebhookDeliveryLog::create([
                'webhook_subscription_id' => $webhook->id,
                'event_type' => $eventType,
                'payload' => $payload,
                'response_code' => $response->status(),
                'response_body' => $response->body(),
                'status' => $response->successful() ? 'delivered' : 'failed',
                'attempt_count' => 1,
                'delivered_at' => $response->successful() ? now() : null,
            ]);

            return $log;
        } catch (\Exception $e) {
            WebhookDeliveryLog::create([
                'webhook_subscription_id' => $webhook->id,
                'event_type' => $eventType,
                'payload' => $payload,
                'status' => 'failed',
                'attempt_count' => 1,
                'error_message' => $e->getMessage(),
                'next_retry_at' => now()->addMinutes(5),
            ]);

            return null;
        }
    }

    /**
     * Retry failed webhooks
     */
    public function retryFailedWebhooks()
    {
        $logs = WebhookDeliveryLog::where('status', 'retrying')
            ->where('next_retry_at', '<=', now())
            ->where('attempt_count', '<', 5)
            ->get();

        foreach ($logs as $log) {
            $webhook = $log->subscription;
            
            try {
                $response = Http::timeout(30)->post($webhook->webhook_url, [
                    'event' => $log->event_type,
                    'payload' => $log->payload,
                    'timestamp' => now()->timestamp,
                    'retry_attempt' => $log->attempt_count + 1,
                ]);

                if ($response->successful()) {
                    $log->update([
                        'status' => 'delivered',
                        'attempt_count' => $log->attempt_count + 1,
                        'delivered_at' => now(),
                    ]);
                } else {
                    $log->update([
                        'status' => 'retrying',
                        'attempt_count' => $log->attempt_count + 1,
                        'next_retry_at' => now()->addMinutes(5 * $log->attempt_count),
                        'response_code' => $response->status(),
                    ]);
                }
            } catch (\Exception $e) {
                $log->update([
                    'status' => 'retrying',
                    'attempt_count' => $log->attempt_count + 1,
                    'error_message' => $e->getMessage(),
                    'next_retry_at' => now()->addMinutes(5 * $log->attempt_count),
                ]);
            }
        }
    }

    /**
     * Get webhook logs
     */
    public function getWebhookLogs($webhookId, $limit = 50)
    {
        return WebhookDeliveryLog::where('webhook_subscription_id', $webhookId)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get subscriptions
     */
    public function getSubscriptions($eventType = null)
    {
        $query = WebhookSubscription::where('is_active', true);

        if ($eventType) {
            $query->where('event_type', $eventType);
        }

        return $query->get();
    }
}
