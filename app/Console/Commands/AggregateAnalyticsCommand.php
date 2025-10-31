<?php

namespace App\Console\Commands;

use App\Services\AnalyticsService;
use App\Services\MetricsService;
use Illuminate\Console\Command;

class AggregateAnalyticsCommand extends Command
{
    protected $signature = 'analytics:aggregate';
    protected $description = 'Aggregate analytics data for reporting';

    public function __construct(
        private AnalyticsService $analyticsService,
        private MetricsService $metricsService
    ) {
        parent::__construct();
    }

    public function handle()
    {
        $this->info('Starting analytics aggregation...');

        try {
            // Aggregate events by date
            $events = \App\Models\AnalyticsEvent::whereDate('created_at', today())
                ->groupBy('event_type')
                ->selectRaw('event_type, COUNT(*) as count')
                ->get();

            foreach ($events as $event) {
                $this->metricsService->aggregateMetrics(
                    'events_' . $event->event_type,
                    today(),
                    'daily',
                    $event->count
                );
            }

            $this->info('Analytics aggregation completed successfully!');
        } catch (\Exception $e) {
            $this->error('Analytics aggregation failed: ' . $e->getMessage());
        }
    }
}
