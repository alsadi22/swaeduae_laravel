<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ScheduledReport;
use Illuminate\Validation\Rule;

class ScheduledReportController extends Controller
{
    /**
     * Display a listing of scheduled reports.
     */
    public function index()
    {
        $scheduledReports = ScheduledReport::latest()->paginate(15);
        
        return view('admin.scheduled-reports.index', compact('scheduledReports'));
    }

    /**
     * Show the form for creating a new scheduled report.
     */
    public function create()
    {
        return view('admin.scheduled-reports.create');
    }

    /**
     * Store a newly created scheduled report in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|in:user,organization,event,certificate,attendance',
            'format' => 'required|string|in:pdf,excel,csv',
            'frequency' => 'required|string|in:daily,weekly,monthly',
            'time' => 'required|date_format:H:i',
            'recipients' => 'required|string',
            'is_active' => 'boolean',
        ]);

        $recipients = array_map('trim', explode(',', $request->recipients));

        $scheduledReport = ScheduledReport::create([
            'name' => $request->name,
            'type' => $request->type,
            'format' => $request->format,
            'frequency' => $request->frequency,
            'time' => $request->time,
            'recipients' => $recipients,
            'is_active' => $request->is_active ?? true,
            'next_run_at' => $this->calculateNextRunTime($request->frequency, $request->time),
        ]);

        return redirect()->route('admin.scheduled-reports.index')
            ->with('success', 'Scheduled report created successfully.');
    }

    /**
     * Display the specified scheduled report.
     */
    public function show(ScheduledReport $scheduledReport)
    {
        return view('admin.scheduled-reports.show', compact('scheduledReport'));
    }

    /**
     * Show the form for editing the specified scheduled report.
     */
    public function edit(ScheduledReport $scheduledReport)
    {
        return view('admin.scheduled-reports.edit', compact('scheduledReport'));
    }

    /**
     * Update the specified scheduled report in storage.
     */
    public function update(Request $request, ScheduledReport $scheduledReport)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|in:user,organization,event,certificate,attendance',
            'format' => 'required|string|in:pdf,excel,csv',
            'frequency' => 'required|string|in:daily,weekly,monthly',
            'time' => 'required|date_format:H:i',
            'recipients' => 'required|string',
            'is_active' => 'boolean',
        ]);

        $recipients = array_map('trim', explode(',', $request->recipients));

        $scheduledReport->update([
            'name' => $request->name,
            'type' => $request->type,
            'format' => $request->format,
            'frequency' => $request->frequency,
            'time' => $request->time,
            'recipients' => $recipients,
            'is_active' => $request->is_active ?? true,
            'next_run_at' => $this->calculateNextRunTime($request->frequency, $request->time),
        ]);

        return redirect()->route('admin.scheduled-reports.index')
            ->with('success', 'Scheduled report updated successfully.');
    }

    /**
     * Remove the specified scheduled report from storage.
     */
    public function destroy(ScheduledReport $scheduledReport)
    {
        $scheduledReport->delete();

        return redirect()->route('admin.scheduled-reports.index')
            ->with('success', 'Scheduled report deleted successfully.');
    }

    /**
     * Toggle the active status of a scheduled report.
     */
    public function toggleActive(ScheduledReport $scheduledReport)
    {
        $scheduledReport->update([
            'is_active' => !$scheduledReport->is_active,
            'next_run_at' => $scheduledReport->is_active ? null : $this->calculateNextRunTime($scheduledReport->frequency, $scheduledReport->time),
        ]);

        $status = $scheduledReport->is_active ? 'activated' : 'deactivated';

        return redirect()->back()
            ->with('success', "Scheduled report {$status} successfully.");
    }

    /**
     * Calculate the next run time based on frequency and time.
     */
    private function calculateNextRunTime($frequency, $time)
    {
        $now = now();
        $runTime = strtotime($time);

        switch ($frequency) {
            case 'daily':
                $nextRun = $now->copy()->startOfDay()->addSeconds($runTime);
                if ($nextRun->isPast()) {
                    $nextRun->addDay();
                }
                break;
            case 'weekly':
                $nextRun = $now->copy()->startOfWeek()->addSeconds($runTime);
                if ($nextRun->isPast()) {
                    $nextRun->addWeek();
                }
                break;
            case 'monthly':
                $nextRun = $now->copy()->startOfMonth()->addSeconds($runTime);
                if ($nextRun->isPast()) {
                    $nextRun->addMonth();
                }
                break;
            default:
                $nextRun = $now->copy()->addDay();
        }

        return $nextRun;
    }
}