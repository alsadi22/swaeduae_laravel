<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ReportingService;
use App\Models\Report;
use App\Models\ReportInstance;
use App\Models\CohortAnalysis;
use App\Models\FunnelAnalytic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    protected $reportingService;

    public function __construct(ReportingService $reportingService)
    {
        $this->reportingService = $reportingService;
    }

    /**
     * List reports
     */
    public function index()
    {
        $reports = $this->reportingService->getUserReports(Auth::id());

        return response()->json($reports);
    }

    /**
     * Create report
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:reports',
            'report_type' => 'required|in:executive,detailed,financial,operational',
            'description' => 'nullable|string',
            'sections' => 'nullable|array',
        ]);

        $report = $this->reportingService->createReport(
            $validated['name'],
            $validated['report_type'],
            $validated['description'] ?? null,
            $validated['sections'] ?? [],
            Auth::id()
        );

        return response()->json($report, 201);
    }

    /**
     * Get report details
     */
    public function show($reportId)
    {
        $report = Report::findOrFail($reportId);

        return response()->json($report);
    }

    /**
     * Generate report instance
     */
    public function generate($reportId, Request $request)
    {
        $validated = $request->validate([
            'format' => 'in:pdf,excel,json',
            'data' => 'nullable|array',
        ]);

        $instance = $this->reportingService->generateReport(
            $reportId,
            $validated['data'] ?? [],
            $validated['format'] ?? 'pdf'
        );

        return response()->json($instance, 201);
    }

    /**
     * Get report instances
     */
    public function instances($reportId)
    {
        $instances = ReportInstance::where('report_id', $reportId)
            ->orderBy('date_generated', 'desc')
            ->paginate(20);

        return response()->json($instances);
    }

    /**
     * Get cohort analysis
     */
    public function cohortAnalysis(Request $request)
    {
        $validated = $request->validate([
            'cohort_name' => 'required|string',
        ]);

        $data = $this->reportingService->getCohortRetention($validated['cohort_name']);

        return response()->json($data);
    }

    /**
     * Get funnel analysis
     */
    public function funnelAnalysis($funnelName)
    {
        $funnel = FunnelAnalytic::where('funnel_name', $funnelName)
            ->latest('date')
            ->firstOrFail();

        return response()->json($funnel);
    }

    /**
     * Publish report
     */
    public function publish($reportId)
    {
        $report = $this->reportingService->publishReport($reportId);

        return response()->json($report);
    }

    /**
     * Schedule report
     */
    public function schedule(Request $request, $reportId)
    {
        $validated = $request->validate([
            'frequency' => 'required|in:daily,weekly,monthly',
            'time' => 'required|date_format:H:i',
            'recipients' => 'nullable|array',
        ]);

        $scheduled = $this->reportingService->scheduleReport(
            $reportId,
            $validated['frequency'],
            $validated['time'],
            $validated['recipients'] ?? []
        );

        return response()->json($scheduled, 201);
    }
}
