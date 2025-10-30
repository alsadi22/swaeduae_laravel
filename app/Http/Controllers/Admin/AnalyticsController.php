<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Organization;
use App\Models\Event;
use App\Models\Application;
use App\Models\Certificate;
use App\Models\Attendance;
use Carbon\Carbon;
use App\Services\ReportExportService;

class AnalyticsController extends Controller
{
    protected $exportService;

    public function __construct(ReportExportService $exportService)
    {
        $this->exportService = $exportService;
    }

    public function index()
    {
        // Get statistics for the dashboard
        $stats = [
            'total_users' => User::count(),
            'total_organizations' => Organization::count(),
            'total_events' => Event::count(),
            'total_applications' => Application::count(),
            'total_certificates' => Certificate::count(),
            'total_attendances' => Attendance::count(),
        ];

        // Get recent activity
        $recentActivity = [
            'recent_users' => User::latest()->take(5)->get(),
            'recent_events' => Event::latest()->take(5)->get(),
            'recent_organizations' => Organization::latest()->take(5)->get(),
        ];

        // Get user statistics by role
        $userStats = [
            'admins' => User::role('admin')->count(),
            'organizations' => User::role('organization')->count(),
            'volunteers' => User::role('volunteer')->count(),
        ];

        // Get organization statistics by status
        $organizationStats = [
            'pending' => Organization::where('status', 'pending')->count(),
            'approved' => Organization::where('status', 'approved')->count(),
            'rejected' => Organization::where('status', 'rejected')->count(),
        ];

        // Get event participation metrics
        $eventStats = [
            'upcoming' => Event::where('start_date', '>', Carbon::now())->count(),
            'ongoing' => Event::where('start_date', '<=', Carbon::now())->where('end_date', '>=', Carbon::now())->count(),
            'completed' => Event::where('end_date', '<', Carbon::now())->count(),
        ];

        // Get certificate issuance tracking
        $certificateStats = [
            'this_month' => Certificate::whereMonth('created_at', Carbon::now()->month)->count(),
            'this_year' => Certificate::whereYear('created_at', Carbon::now()->year)->count(),
            'total' => Certificate::count(),
        ];

        // Get trend data for the last 6 months
        $trendData = $this->getTrendData();

        return view('admin.analytics.index', compact(
            'stats', 
            'recentActivity', 
            'userStats', 
            'organizationStats', 
            'eventStats', 
            'certificateStats',
            'trendData'
        ));
    }

    public function reports()
    {
        // This will be for the custom report builder
        return view('admin.analytics.reports');
    }

    public function export(Request $request)
    {
        $format = $request->get('format', 'pdf');
        $type = $request->get('type', 'general');
        
        // Generate report data
        $dates = $this->getDateRange('month'); // Default to this month
        $reportData = $this->generateReportData($type, $dates['start'], $dates['end']);
        
        // Generate filename
        $filename = 'swaeduae_' . $type . '_report_' . date('Y-m-d');
        
        // Export the report
        return $this->exportService->export($reportData, $format, $filename);
    }

    public function generateReport(Request $request)
    {
        $type = $request->get('type', 'user');
        $dateRange = $request->get('date_range', 'month');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        $format = $request->get('format', 'pdf');

        // Determine date range
        $dates = $this->getDateRange($dateRange, $startDate, $endDate);

        // Generate report data based on type
        $reportData = $this->generateReportData($type, $dates['start'], $dates['end']);

        // If format is specified and not JSON, export directly
        if ($format !== 'json') {
            $filename = 'swaeduae_' . $type . '_report_' . date('Y-m-d');
            return $this->exportService->export($reportData, $format, $filename);
        }

        // Return JSON response for AJAX requests
        return response()->json([
            'type' => $type,
            'date_range' => $dateRange,
            'start_date' => $dates['start']->format('Y-m-d'),
            'end_date' => $dates['end']->format('Y-m-d'),
            'data' => $reportData,
            'format' => $format
        ]);
    }

    private function getDateRange($range, $startDate = null, $endDate = null)
    {
        $now = Carbon::now();

        switch ($range) {
            case 'today':
                $start = $now->startOfDay();
                $end = $now->endOfDay();
                break;
            case 'week':
                $start = $now->startOfWeek();
                $end = $now->endOfWeek();
                break;
            case 'month':
                $start = $now->startOfMonth();
                $end = $now->endOfMonth();
                break;
            case 'year':
                $start = $now->startOfYear();
                $end = $now->endOfYear();
                break;
            case 'custom':
                $start = $startDate ? Carbon::parse($startDate)->startOfDay() : $now->startOfMonth();
                $end = $endDate ? Carbon::parse($endDate)->endOfDay() : $now->endOfMonth();
                break;
            default:
                $start = $now->startOfMonth();
                $end = $now->endOfMonth();
        }

        return ['start' => $start, 'end' => $end];
    }

    private function generateReportData($type, $startDate, $endDate)
    {
        switch ($type) {
            case 'user':
                return $this->generateUserReport($startDate, $endDate);
            case 'organization':
                return $this->generateOrganizationReport($startDate, $endDate);
            case 'event':
                return $this->generateEventReport($startDate, $endDate);
            case 'certificate':
                return $this->generateCertificateReport($startDate, $endDate);
            case 'attendance':
                return $this->generateAttendanceReport($startDate, $endDate);
            default:
                return $this->generateUserReport($startDate, $endDate);
        }
    }

    private function generateUserReport($startDate, $endDate)
    {
        $newUsers = User::whereBetween('created_at', [$startDate, $endDate])->count();
        $activeUsers = User::whereHas('applications', function ($query) use ($startDate, $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        })->count();

        return [
            'report_title' => 'User Statistics Report',
            'metrics' => [
                'New Users' => $newUsers,
                'Active Users' => $activeUsers,
                'User Growth Rate' => $newUsers > 0 ? round(($newUsers / User::count()) * 100, 2) . '%' : '0%',
            ],
            'chart_data' => [
                'labels' => ['New Users', 'Active Users'],
                'values' => [$newUsers, $activeUsers]
            ]
        ];
    }

    private function generateOrganizationReport($startDate, $endDate)
    {
        $newOrganizations = Organization::whereBetween('created_at', [$startDate, $endDate])->count();
        $approvedOrganizations = Organization::where('status', 'approved')
            ->whereBetween('updated_at', [$startDate, $endDate])->count();

        return [
            'report_title' => 'Organization Statistics Report',
            'metrics' => [
                'New Organizations' => $newOrganizations,
                'Approved Organizations' => $approvedOrganizations,
                'Approval Rate' => $newOrganizations > 0 ? round(($approvedOrganizations / $newOrganizations) * 100, 2) . '%' : '0%',
            ],
            'chart_data' => [
                'labels' => ['New Organizations', 'Approved Organizations'],
                'values' => [$newOrganizations, $approvedOrganizations]
            ]
        ];
    }

    private function generateEventReport($startDate, $endDate)
    {
        $newEvents = Event::whereBetween('created_at', [$startDate, $endDate])->count();
        $completedEvents = Event::where('end_date', '<', $endDate)
            ->where('end_date', '>', $startDate)->count();
        $upcomingEvents = Event::where('start_date', '>', $endDate)->count();

        return [
            'report_title' => 'Event Statistics Report',
            'metrics' => [
                'New Events' => $newEvents,
                'Completed Events' => $completedEvents,
                'Upcoming Events' => $upcomingEvents,
            ],
            'chart_data' => [
                'labels' => ['New Events', 'Completed Events', 'Upcoming Events'],
                'values' => [$newEvents, $completedEvents, $upcomingEvents]
            ]
        ];
    }

    private function generateCertificateReport($startDate, $endDate)
    {
        $newCertificates = Certificate::whereBetween('created_at', [$startDate, $endDate])->count();
        $verifiedCertificates = Certificate::where('is_verified', true)
            ->whereBetween('verified_at', [$startDate, $endDate])->count();

        return [
            'report_title' => 'Certificate Statistics Report',
            'metrics' => [
                'Certificates Issued' => $newCertificates,
                'Certificates Verified' => $verifiedCertificates,
                'Verification Rate' => $newCertificates > 0 ? round(($verifiedCertificates / $newCertificates) * 100, 2) . '%' : '0%',
            ],
            'chart_data' => [
                'labels' => ['Certificates Issued', 'Certificates Verified'],
                'values' => [$newCertificates, $verifiedCertificates]
            ]
        ];
    }

    private function generateAttendanceReport($startDate, $endDate)
    {
        $totalAttendance = Attendance::whereBetween('created_at', [$startDate, $endDate])->count();
        $checkedIn = Attendance::where('status', 'checked_in')
            ->whereBetween('created_at', [$startDate, $endDate])->count();
        $checkedOut = Attendance::where('status', 'checked_out')
            ->whereBetween('created_at', [$startDate, $endDate])->count();

        return [
            'report_title' => 'Attendance Statistics Report',
            'metrics' => [
                'Total Attendance Records' => $totalAttendance,
                'Checked In' => $checkedIn,
                'Checked Out' => $checkedOut,
            ],
            'chart_data' => [
                'labels' => ['Total Records', 'Checked In', 'Checked Out'],
                'values' => [$totalAttendance, $checkedIn, $checkedOut]
            ]
        ];
    }

    /**
     * Get trend data for the last 6 months
     */
    private function getTrendData()
    {
        $months = [];
        $userTrends = [];
        $eventTrends = [];
        $certificateTrends = [];
        $attendanceTrends = [];

        // Get data for the last 6 months
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $monthName = $date->format('M Y');
            
            $months[] = $monthName;
            
            // User trends
            $userCount = User::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
            $userTrends[] = $userCount;
            
            // Event trends
            $eventCount = Event::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
            $eventTrends[] = $eventCount;
            
            // Certificate trends
            $certificateCount = Certificate::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
            $certificateTrends[] = $certificateCount;
            
            // Attendance trends
            $attendanceCount = Attendance::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
            $attendanceTrends[] = $attendanceCount;
        }

        return [
            'months' => $months,
            'users' => $userTrends,
            'events' => $eventTrends,
            'certificates' => $certificateTrends,
            'attendances' => $attendanceTrends,
        ];
    }

    /**
     * Get detailed trend analysis
     */
    public function trends(Request $request)
    {
        $period = $request->get('period', '6months');
        $metric = $request->get('metric', 'users');
        
        $trendData = $this->getDetailedTrendData($period, $metric);
        
        return response()->json($trendData);
    }

    /**
     * Get detailed trend data based on period and metric
     */
    private function getDetailedTrendData($period, $metric)
    {
        $dates = [];
        $values = [];

        switch ($period) {
            case '1year':
                // Monthly data for 1 year
                for ($i = 11; $i >= 0; $i--) {
                    $date = Carbon::now()->subMonths($i);
                    $dates[] = $date->format('M Y');
                    $values[] = $this->getMetricValueForDate($metric, $date);
                }
                break;
            case '30days':
                // Daily data for 30 days
                for ($i = 29; $i >= 0; $i--) {
                    $date = Carbon::now()->subDays($i);
                    $dates[] = $date->format('M j');
                    $values[] = $this->getMetricValueForDate($metric, $date, 'day');
                }
                break;
            case '7days':
                // Daily data for 7 days
                for ($i = 6; $i >= 0; $i--) {
                    $date = Carbon::now()->subDays($i);
                    $dates[] = $date->format('D');
                    $values[] = $this->getMetricValueForDate($metric, $date, 'day');
                }
                break;
            case '6months':
            default:
                // Monthly data for 6 months
                for ($i = 5; $i >= 0; $i--) {
                    $date = Carbon::now()->subMonths($i);
                    $dates[] = $date->format('M Y');
                    $values[] = $this->getMetricValueForDate($metric, $date);
                }
                break;
        }

        return [
            'dates' => $dates,
            'values' => $values,
            'metric' => $metric,
            'period' => $period
        ];
    }

    /**
     * Get metric value for a specific date
     */
    private function getMetricValueForDate($metric, $date, $granularity = 'month')
    {
        switch ($metric) {
            case 'events':
                if ($granularity === 'day') {
                    return Event::whereDate('created_at', $date)->count();
                } else {
                    return Event::whereYear('created_at', $date->year)
                        ->whereMonth('created_at', $date->month)
                        ->count();
                }
            case 'certificates':
                if ($granularity === 'day') {
                    return Certificate::whereDate('created_at', $date)->count();
                } else {
                    return Certificate::whereYear('created_at', $date->year)
                        ->whereMonth('created_at', $date->month)
                        ->count();
                }
            case 'attendances':
                if ($granularity === 'day') {
                    return Attendance::whereDate('created_at', $date)->count();
                } else {
                    return Attendance::whereYear('created_at', $date->year)
                        ->whereMonth('created_at', $date->month)
                        ->count();
                }
            case 'users':
            default:
                if ($granularity === 'day') {
                    return User::whereDate('created_at', $date)->count();
                } else {
                    return User::whereYear('created_at', $date->year)
                        ->whereMonth('created_at', $date->month)
                        ->count();
                }
        }
    }
}