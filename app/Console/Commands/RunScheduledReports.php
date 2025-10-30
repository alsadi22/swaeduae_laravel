<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ScheduledReport;
use App\Services\ReportExportService;
use Illuminate\Support\Facades\Mail;
use App\Mail\ScheduledReportMail;

class RunScheduledReports extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scheduled-reports:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run scheduled reports that are due';

    /**
     * Execute the console command.
     */
    public function handle(ReportExportService $exportService)
    {
        $this->info('Running scheduled reports...');

        // Get active scheduled reports that are due
        $scheduledReports = ScheduledReport::active()
            ->where('next_run_at', '<=', now())
            ->get();

        if ($scheduledReports->isEmpty()) {
            $this->info('No scheduled reports are due to run.');
            return;
        }

        $this->info("Found {$scheduledReports->count()} scheduled reports to run.");

        foreach ($scheduledReports as $scheduledReport) {
            $this->info("Running report: {$scheduledReport->name}");

            try {
                // Generate report data
                $analyticsController = new \App\Http\Controllers\Admin\AnalyticsController($exportService);
                $dates = $analyticsController->getDateRange('month'); // Default to this month
                $reportData = $analyticsController->generateReportData($scheduledReport->type, $dates['start'], $dates['end']);

                // Generate filename
                $filename = 'scheduled_' . $scheduledReport->type . '_report_' . date('Y-m-d');

                // Export the report
                $exportedReport = $exportService->export($reportData, $scheduledReport->format, $filename);

                // Send email to recipients
                foreach ($scheduledReport->recipients as $recipient) {
                    Mail::to($recipient)->send(new ScheduledReportMail($scheduledReport, $exportedReport, $filename));
                }

                // Update the scheduled report
                $scheduledReport->update([
                    'last_run_at' => now(),
                    'next_run_at' => $scheduledReport->calculateNextRunTime(),
                ]);

                $this->info("Successfully ran report: {$scheduledReport->name}");
            } catch (\Exception $e) {
                $this->error("Failed to run report {$scheduledReport->name}: {$e->getMessage()}");
                // Log the error but continue with other reports
                continue;
            }
        }

        $this->info('Finished running scheduled reports.');
    }
}