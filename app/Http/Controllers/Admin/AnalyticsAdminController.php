<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ReportingService;
use App\Services\MetricsService;
use App\Models\KpiMetric;
use App\Models\AlertRule;
use App\Models\AlertNotification;
use Illuminate\Http\Request;

class AnalyticsAdminController extends Controller
{
    protected $reportingService;
    protected $metricsService;

    public function __construct(ReportingService $reportingService, MetricsService $metricsService)
    {
        $this->reportingService = $reportingService;
        $this->metricsService = $metricsService;
    }

    /**
     * Dashboard overview
     */
    public function dashboard()
    {
        $metrics = $this->metricsService->getKpiSummary();
        $activeAlerts = $this->reportingService->getActiveAlerts();

        return view('admin.analytics.dashboard', compact('metrics', 'activeAlerts'));
    }

    /**
     * KPI management
     */
    public function kpiManagement()
    {
        $kpis = KpiMetric::paginate(20);

        return view('admin.analytics.kpi-management', compact('kpis'));
    }

    /**
     * Create KPI
     */
    public function createKpi(Request $request)
    {
        $validated = $request->validate([
            'metric_name' => 'required|string|unique:kpi_metrics',
            'display_name' => 'required|string',
            'category' => 'required|string',
            'calculation_method' => 'required|in:sum,count,average,percentage',
            'unit' => 'nullable|string',
        ]);

        $kpi = $this->metricsService->createKpiMetric(
            $validated['metric_name'],
            $validated['display_name'],
            $validated['category'],
            $validated['calculation_method'],
            $validated['formula'] ?? null
        );

        return back()->with('success', 'KPI created successfully');
    }

    /**
     * Alert management
     */
    public function alertManagement()
    {
        $alerts = AlertRule::paginate(20);
        $notifications = AlertNotification::where('status', 'pending')->get();

        return view('admin.analytics.alert-management', compact('alerts', 'notifications'));
    }

    /**
     * Create alert rule
     */
    public function createAlert(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'metric_name' => 'required|string',
            'condition' => 'required|in:above,below,equals,changes',
            'threshold' => 'nullable|numeric',
            'frequency' => 'required|in:immediate,daily,weekly',
            'recipients' => 'nullable|array',
        ]);

        $rule = $this->reportingService->createAlertRule(
            $validated['name'],
            $validated['metric_name'],
            $validated['condition'],
            $validated['threshold'],
            $validated['frequency'],
            $validated['recipients'] ?? []
        );

        return back()->with('success', 'Alert rule created');
    }

    /**
     * Acknowledge alert
     */
    public function acknowledgeAlert($alertId)
    {
        $this->reportingService->acknowledgeAlert($alertId);

        return back()->with('success', 'Alert acknowledged');
    }

    /**
     * Resolve alert
     */
    public function resolveAlert($alertId)
    {
        $this->reportingService->resolveAlert($alertId);

        return back()->with('success', 'Alert resolved');
    }

    /**
     * Report management
     */
    public function reportManagement()
    {
        $reports = \App\Models\Report::paginate(20);

        return view('admin.analytics.report-management', compact('reports'));
    }

    /**
     * Record KPI value
     */
    public function recordKpiValue(Request $request)
    {
        $validated = $request->validate([
            'metric_name' => 'required|string',
            'date' => 'required|date',
            'value' => 'required|numeric',
            'target_value' => 'nullable|numeric',
        ]);

        $this->metricsService->recordKpiValue(
            $validated['metric_name'],
            $validated['date'],
            $validated['value'],
            $validated['target_value'] ?? null
        );

        return back()->with('success', 'KPI value recorded');
    }

    /**
     * View analytics by category
     */
    public function analyticsBy($category)
    {
        $metrics = $this->metricsService->getKpiSummary($category);

        return view('admin.analytics.category-view', compact('category', 'metrics'));
    }
}
