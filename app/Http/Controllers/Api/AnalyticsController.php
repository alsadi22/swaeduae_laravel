<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\AnalyticsService;
use App\Services\MetricsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AnalyticsController extends Controller
{
    protected $analyticsService;
    protected $metricsService;

    public function __construct(AnalyticsService $analyticsService, MetricsService $metricsService)
    {
        $this->analyticsService = $analyticsService;
        $this->metricsService = $metricsService;
    }

    /**
     * Get dashboard metrics
     */
    public function dashboard()
    {
        $metrics = $this->metricsService->getKpiSummary();

        return response()->json($metrics);
    }

    /**
     * Get KPI by date range
     */
    public function kpiByRange(Request $request)
    {
        $validated = $request->validate([
            'metric_name' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
        ]);

        $data = $this->metricsService->getKpiByDateRange(
            $validated['metric_name'],
            $validated['start_date'],
            $validated['end_date']
        );

        return response()->json($data);
    }

    /**
     * Get metric trend
     */
    public function metricTrend(Request $request)
    {
        $validated = $request->validate([
            'metric_name' => 'required|string',
            'days' => 'integer|min:1|max:365',
        ]);

        $trend = $this->metricsService->getMetricTrend(
            $validated['metric_name'],
            $validated['days'] ?? 30
        );

        return response()->json($trend);
    }

    /**
     * Get growth rate
     */
    public function growthRate(Request $request)
    {
        $validated = $request->validate([
            'metric_name' => 'required|string',
            'period' => 'in:week,month,year',
        ]);

        $rate = $this->metricsService->calculateGrowthRate(
            $validated['metric_name'],
            $validated['period'] ?? 'month'
        );

        return response()->json(['growth_rate' => $rate]);
    }

    /**
     * Get popular pages
     */
    public function popularPages(Request $request)
    {
        $validated = $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'limit' => 'integer|min:1|max:50',
        ]);

        $pages = $this->analyticsService->getPopularPages(
            $validated['start_date'],
            $validated['end_date'],
            $validated['limit'] ?? 10
        );

        return response()->json($pages);
    }

    /**
     * Get conversion funnel
     */
    public function conversionFunnel(Request $request)
    {
        $validated = $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date',
        ]);

        $funnel = $this->analyticsService->getConversionFunnel(
            $validated['start_date'],
            $validated['end_date']
        );

        return response()->json($funnel);
    }

    /**
     * Get geographic distribution
     */
    public function geographicDistribution(Request $request)
    {
        $validated = $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date',
        ]);

        $distribution = $this->analyticsService->getGeographicDistribution(
            $validated['start_date'],
            $validated['end_date']
        );

        return response()->json($distribution);
    }

    /**
     * Get user sessions
     */
    public function userSessions()
    {
        $sessions = $this->analyticsService->getUserSessions(Auth::id(), 20);

        return response()->json($sessions);
    }

    /**
     * Get KPI summary by category
     */
    public function kpiByCategory(Request $request)
    {
        $category = $request->get('category');

        $metrics = $this->metricsService->getKpiSummary($category);

        return response()->json($metrics);
    }

    /**
     * Get vs target
     */
    public function vsTarget(Request $request)
    {
        $validated = $request->validate([
            'metric_name' => 'required|string',
            'date' => 'required|date',
        ]);

        $comparison = $this->metricsService->getVsTarget(
            $validated['metric_name'],
            $validated['date']
        );

        return response()->json($comparison);
    }
}
