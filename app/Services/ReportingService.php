<?php

namespace App\Services;

use App\Models\Report;
use App\Models\ReportInstance;
use App\Models\ScheduledReport;
use App\Models\CohortAnalysis;
use App\Models\FunnelAnalytic;
use App\Models\AlertRule;
use App\Models\AlertNotification;

class ReportingService
{
    /**
     * Create report
     */
    public function createReport($name, $type, $description = null, $sections = [], $userId = null)
    {
        return Report::create([
            'name' => $name,
            'report_type' => $type,
            'description' => $description,
            'sections' => $sections,
            'created_by' => $userId,
            'status' => 'draft',
        ]);
    }

    /**
     * Publish report
     */
    public function publishReport($reportId)
    {
        $report = Report::findOrFail($reportId);

        $report->update(['status' => 'published']);

        return $report;
    }

    /**
     * Schedule report
     */
    public function scheduleReport($reportId, $frequency, $time, $recipients = [])
    {
        return ScheduledReport::create([
            'report_id' => $reportId,
            'frequency' => $frequency,
            'scheduled_time' => $time,
            'recipients' => $recipients,
            'is_active' => true,
            'next_send_at' => $this->calculateNextSendTime($frequency, $time),
        ]);
    }

    /**
     * Calculate next send time
     */
    private function calculateNextSendTime($frequency, $time)
    {
        $time = \DateTime::createFromFormat('H:i', $time);

        return match($frequency) {
            'daily' => now()->setTime($time->format('H'), $time->format('i'))->addDay(),
            'weekly' => now()->setTime($time->format('H'), $time->format('i'))->addWeek(),
            'monthly' => now()->setTime($time->format('H'), $time->format('i'))->addMonth(),
            default => now(),
        };
    }

    /**
     * Generate report instance
     */
    public function generateReport($reportId, $data = [], $format = 'pdf')
    {
        $report = Report::findOrFail($reportId);

        $instance = ReportInstance::create([
            'report_id' => $reportId,
            'date_generated' => now(),
            'period_start' => now()->subMonth(),
            'period_end' => now(),
            'data' => $data,
            'format' => $format,
            'status' => 'generating',
        ]);

        // TODO: Generate PDF/Excel file
        $instance->update(['status' => 'ready', 'file_path' => '/reports/' . $reportId . '.pdf']);

        return $instance;
    }

    /**
     * Get reports for user
     */
    public function getUserReports($userId, $status = null)
    {
        $query = Report::where('created_by', $userId);

        if ($status) {
            $query->where('status', $status);
        }

        return $query->get();
    }

    /**
     * Perform cohort analysis
     */
    public function performCohortAnalysis($cohortName, $cohortDate, $cohortSize, $periodNumber, $activeUsers, $retentionPercentage = null)
    {
        return CohortAnalysis::create([
            'cohort_name' => $cohortName,
            'cohort_date' => $cohortDate,
            'cohort_size' => $cohortSize,
            'period_number' => $periodNumber,
            'active_users' => $activeUsers,
            'retention_percentage' => $retentionPercentage ?? (($activeUsers / $cohortSize) * 100),
        ]);
    }

    /**
     * Get cohort retention
     */
    public function getCohortRetention($cohortName)
    {
        return CohortAnalysis::where('cohort_name', $cohortName)
            ->orderBy('period_number')
            ->get()
            ->map(fn($row) => [
                'period' => $row->period_number,
                'retention' => $row->retention_percentage,
                'users' => $row->active_users,
            ]);
    }

    /**
     * Create funnel
     */
    public function createFunnel($name, $steps, $description = null)
    {
        return FunnelAnalytic::create([
            'funnel_name' => $name,
            'description' => $description,
            'steps' => $steps,
            'date' => now(),
            'total_users' => 0,
        ]);
    }

    /**
     * Update funnel data
     */
    public function updateFunnelData($funnelId, $stepData, $totalUsers)
    {
        $funnel = FunnelAnalytic::findOrFail($funnelId);

        $completionRate = null;
        if (!empty($stepData)) {
            $lastStepCount = end($stepData)['count'] ?? 0;
            $completionRate = ($lastStepCount / $totalUsers) * 100;
        }

        $funnel->update([
            'step_data' => $stepData,
            'total_users' => $totalUsers,
            'completion_rate' => $completionRate,
        ]);

        return $funnel;
    }

    /**
     * Create alert rule
     */
    public function createAlertRule($name, $metricName, $condition, $threshold, $frequency = 'immediate', $recipients = [])
    {
        return AlertRule::create([
            'name' => $name,
            'metric_name' => $metricName,
            'condition' => $condition,
            'threshold' => $threshold,
            'frequency' => $frequency,
            'recipients' => $recipients,
            'is_active' => true,
        ]);
    }

    /**
     * Check alert condition
     */
    public function checkAlertCondition($ruleId, $currentValue)
    {
        $rule = AlertRule::findOrFail($ruleId);

        $triggered = match($rule->condition) {
            'above' => $currentValue > $rule->threshold,
            'below' => $currentValue < $rule->threshold,
            'equals' => $currentValue === $rule->threshold,
            default => false,
        };

        if ($triggered) {
            $this->triggerAlert($rule, $currentValue);
        }

        return $triggered;
    }

    /**
     * Trigger alert
     */
    private function triggerAlert($rule, $value)
    {
        AlertNotification::create([
            'alert_rule_id' => $rule->id,
            'title' => $rule->name,
            'message' => "{$rule->metric_name} is {$rule->condition} threshold. Current value: {$value}",
            'severity' => $value > ($rule->threshold * 1.5) ? 'critical' : 'warning',
            'status' => 'pending',
        ]);

        $rule->update(['last_triggered_at' => now()]);
    }

    /**
     * Get active alerts
     */
    public function getActiveAlerts()
    {
        return AlertNotification::where('status', 'pending')->get();
    }

    /**
     * Acknowledge alert
     */
    public function acknowledgeAlert($alertId)
    {
        $alert = AlertNotification::findOrFail($alertId);

        $alert->update([
            'status' => 'acknowledged',
            'acknowledged_at' => now(),
        ]);

        return $alert;
    }

    /**
     * Resolve alert
     */
    public function resolveAlert($alertId)
    {
        $alert = AlertNotification::findOrFail($alertId);

        $alert->update([
            'status' => 'resolved',
            'resolved_at' => now(),
        ]);

        return $alert;
    }
}
