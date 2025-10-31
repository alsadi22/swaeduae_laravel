<?php

namespace App\Console\Commands;

use App\Services\WebhookService;
use App\Models\WebhookDeliveryLog;
use Illuminate\Console\Command;

class RetryFailedWebhooksCommand extends Command
{
    protected $signature = 'webhooks:retry-failed';
    protected $description = 'Retry failed webhook deliveries';

    public function __construct(private WebhookService $webhookService)
    {
        parent::__construct();
    }

    public function handle()
    {
        $this->info('Retrying failed webhooks...');

        try {
            $failedLogs = WebhookDeliveryLog::where('status', 'failed')
                ->where('attempt_count', '<', 5)
                ->where('next_retry_at', '<=', now())
                ->get();

            foreach ($failedLogs as $log) {
                try {
                    $this->webhookService->deliverWebhook(
                        $log->webhook_subscription_id,
                        $log->payload
                    );
                    $log->update(['status' => 'delivered', 'delivered_at' => now()]);
                    $this->info("Webhook {$log->id} delivered successfully");
                } catch (\Exception $e) {
                    $attempt = $log->attempt_count + 1;
                    $nextRetry = now()->addMinutes(pow(2, $attempt));
                    
                    $log->update([
                        'attempt_count' => $attempt,
                        'next_retry_at' => $nextRetry,
                        'error_message' => $e->getMessage(),
                        'status' => $attempt >= 5 ? 'failed' : 'retrying',
                    ]);
                }
            }

            $this->info('Webhook retry completed!');
        } catch (\Exception $e) {
            $this->error('Webhook retry failed: ' . $e->getMessage());
        }
    }
}
