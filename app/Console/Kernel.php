<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Database backup - Daily at 2 AM
        $schedule->exec('bash /var/www/swaeduae/swaeduae_laravel/scripts/backup-database.sh')
            ->dailyAt('02:00')
            ->name('swaeduae-backup')
            ->withoutOverlapping();
        
        // Clear expired tokens - Every hour
        $schedule->command('sanctum:prune-expired --hours=24')
            ->hourly()
            ->name('prune-tokens')
            ->withoutOverlapping();
        
        // Queue monitoring - Every 5 minutes
        $schedule->command('queue:monitor')
            ->everyFiveMinutes()
            ->name('queue-monitor')
            ->withoutOverlapping();
        
        // Cache optimization - Daily at 3 AM
        $schedule->call(function () {
            \Illuminate\Support\Facades\Artisan::call('cache:prune-stale-tags');
        })->dailyAt('03:00')
         ->name('cache-cleanup')
         ->withoutOverlapping();
        
        // Session cleanup - Twice daily
        $schedule->call(function () {
            \Illuminate\Support\Facades\DB::table('sessions')
                ->where('last_activity', '<', now()->subDays(7)->timestamp)
                ->delete();
        })->twiceDaily(1, 13)
         ->name('cleanup-sessions')
         ->withoutOverlapping();
        
        // Event status updates - Every minute
        $schedule->call(function () {
            \App\Models\Event::whereDate('end_date', '<', now())
                ->where('status', 'published')
                ->update(['status' => 'completed']);
        })->everyMinute()
         ->name('update-event-status')
         ->withoutOverlapping();
        
        // Generate scheduled reports - Daily at 8 AM
        $schedule->command('reports:generate-scheduled')
            ->dailyAt('08:00')
            ->name('scheduled-reports')
            ->withoutOverlapping();
        
        // Aggregate analytics - Every 30 minutes
        $schedule->call(function () {
            \Artisan::call('analytics:aggregate');
        })->everyThirtyMinutes()
         ->name('analytics-aggregation')
         ->withoutOverlapping();
        
        // Process analytics alerts - Every 5 minutes
        $schedule->call(function () {
            \Artisan::call('analytics:check-alerts');
        })->everyFiveMinutes()
         ->name('analytics-alerts')
         ->withoutOverlapping();
        
        // Send digest notifications - Daily at 9 AM
        $schedule->call(function () {
            \Artisan::call('notifications:send-digests');
        })->dailyAt('09:00')
         ->name('digest-notifications')
         ->withoutOverlapping();
        
        // Process webhook retries - Every 10 minutes
        $schedule->call(function () {
            \Artisan::call('webhooks:retry-failed');
        })->everyTenMinutes()
         ->name('webhook-retries')
         ->withoutOverlapping();
        
        // Update churn predictions - Daily at 1 AM
        $schedule->call(function () {
            \Artisan::call('predictions:update-churn');
        })->dailyAt('01:00')
         ->name('update-churn')
         ->withoutOverlapping();
        
        // Calculate user engagement - Hourly
        $schedule->call(function () {
            \Artisan::call('engagement:calculate-metrics');
        })->hourly()
         ->name('engagement-metrics')
         ->withoutOverlapping();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}