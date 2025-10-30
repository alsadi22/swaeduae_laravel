<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ScheduledReport;
use App\Mail\ScheduledReportMail;
use Illuminate\Support\Facades\Mail;
use App\Services\ReportExportService;

class GenerateScheduledReports extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reports:generate-scheduled';

    /**
     * The description of the console command.
     *
     * @var string
     */
    protected $description = 'Generate and send scheduled reports to recipients';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Generating scheduled reports...');

        // Get all active scheduled reports
        $scheduledReports = ScheduledReport::where('is_active', true)
            ->where(function ($query) {
                // Check if report should run today
                $today = date('N'); // 1-7 (Monday-Sunday)
                $dayOfMonth = date('d');
                
                $query->where('frequency', 'daily')
                    ->orWhere(function ($q) use ($today) {
                        $q->where('frequency', 'weekly')
                          ->whereJsonContains('schedule_data->days', (int)$today);
                    })
                    ->orWhere(function ($q) use ($dayOfMonth) {
                        $q->where('frequency', 'monthly')
                          ->where('schedule_data->day_of_month', (int)$dayOfMonth);
                    });
            })
            ->get();

        if ($scheduledReports->isEmpty()) {
            $this->info('No scheduled reports to generate.');
            return 0;
        }

        $reportService = app(ReportExportService::class);
        $successCount = 0;
        $failureCount = 0;

        foreach ($scheduledReports as $scheduledReport) {
            try {
                $this->info("Generating report: {$scheduledReport->name}");

                // Build report parameters
                $parameters = $scheduledReport->report_parameters ?? [];
                
                // Generate report
                $reportData = $reportService->generateReport(
                    $scheduledReport->report_type,
                    $parameters
                );

                // Export to requested format
                $exportedFile = $reportService->exportReport(
                    $reportData,
                    $scheduledReport->export_format ?? 'pdf'
                );

                // Send email to recipients
                foreach ($scheduledReport->recipients as $recipient) {
                    Mail::to($recipient)->send(new ScheduledReportMail(
                        $scheduledReport,
                        $exportedFile
                    ));
                }

                // Update last sent timestamp
                $scheduledReport->update([
                    'last_sent_at' => now(),
                    'next_send_at' => $this->calculateNextSendTime($scheduledReport)
                ]);

                $this->info("✓ Report sent to " . count($scheduledReport->recipients) . " recipients");
                $successCount++;

            } catch (\Exception $e) {
                $this->error("✗ Failed to generate report: " . $e->getMessage());
                $failureCount++;
                
                // Log the error
                \Illuminate\Support\Facades\Log::error("Scheduled report generation failed", [
                    'report_id' => $scheduledReport->id,
                    'error' => $e->getMessage()
                ]);
            }
        }

        $this->info("\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");
        $this->info("Report Generation Summary:");
        $this->info("✓ Successful: " . $successCount);
        $this->error("✗ Failed: " . $failureCount);
        $this->info("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n");

        return $failureCount > 0 ? 1 : 0;
    }

    /**
     * Calculate the next send time for a scheduled report
     */
    private function calculateNextSendTime($report)
    {
        $frequency = $report->frequency;
        $now = now();

        switch ($frequency) {
            case 'daily':
                return $now->addDay()->setTime(8, 0, 0);
            case 'weekly':
                return $now->addWeek()->setTime(8, 0, 0);
            case 'monthly':
                return $now->addMonth()->setTime(8, 0, 0);
            default:
                return $now->addDay();
        }
    }
}
