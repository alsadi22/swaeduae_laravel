<?php

namespace App\Console\Commands;

use App\Services\MetricsService;
use App\Models\AlertRule;
use App\Models\AlertNotification;
use Illuminate\Console\Command;

class CheckAnalyticsAlertsCommand extends Command
{
    protected $signature = 'analytics:check-alerts';
    protected $description = 'Check and trigger analytics alerts based on thresholds';

    public function __construct(private MetricsService $metricsService)
    {
        parent::__construct();
    }

    public function handle()
    {
        $this->info('Checking analytics alerts...');

        try {
            $rules = AlertRule::where('is_active', true)->get();

            foreach ($rules as $rule) {
                $currentValue = $this->metricsService->getKpiByDateRange(
                    $rule->metric_name,
                    now()->subDay(),
                    now()
                );

                if ($this->shouldTriggerAlert($rule, $currentValue)) {
                    AlertNotification::create([
                        'alert_rule_id' => $rule->id,
                        'triggered_at' => now(),
                        'current_value' => $currentValue,
                        'status' => 'pending',
                    ]);

                    $rule->update(['last_triggered_at' => now()]);
                    $this->info("Alert triggered for: {$rule->name}");
                }
            }

            $this->info('Alert checking completed!');
        } catch (\Exception $e) {
            $this->error('Alert checking failed: ' . $e->getMessage());
        }
    }

    private function shouldTriggerAlert($rule, $currentValue)
    {
        return match($rule->condition) {
            'above' => $currentValue > $rule->threshold,
            'below' => $currentValue < $rule->threshold,
            'equals' => $currentValue == $rule->threshold,
            'changes' => true,
            default => false,
        };
    }
}
