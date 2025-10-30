<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Organization;
use App\Models\Event;
use App\Models\Application;
use App\Models\Certificate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    /**
     * Display the main reports dashboard.
     */
    public function index()
    {
        // Get statistics
        $totalUsers = User::count();
        $totalOrganizations = Organization::count();
        $totalEvents = Event::count();
        $totalApplications = Application::count();
        $totalCertificates = Certificate::count();
        
        // Get recent activity
        $recentUsers = User::latest()->take(5)->get();
        $recentOrganizations = Organization::latest()->take(5)->get();
        $recentEvents = Event::latest()->take(5)->get();
        
        // Get monthly trends for the last 6 months
        $monthlyUserTrends = User::select(
            DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
            DB::raw('COUNT(*) as count')
        )
        ->where('created_at', '>=', now()->subMonths(6))
        ->groupBy('month')
        ->orderBy('month')
        ->get();
        
        $monthlyEventTrends = Event::select(
            DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
            DB::raw('COUNT(*) as count')
        )
        ->where('created_at', '>=', now()->subMonths(6))
        ->groupBy('month')
        ->orderBy('month')
        ->get();
        
        // Calculate max values for trends
        $maxUserTrend = $monthlyUserTrends->max('count') ?? 1;
        $maxEventTrend = $monthlyEventTrends->max('count') ?? 1;
        
        return view('admin.reports.index', compact(
            'totalUsers',
            'totalOrganizations', 
            'totalEvents',
            'totalApplications',
            'totalCertificates',
            'recentUsers',
            'recentOrganizations',
            'recentEvents',
            'monthlyUserTrends',
            'monthlyEventTrends',
            'maxUserTrend',
            'maxEventTrend'
        ));
    }

    /**
     * Display user reports.
     */
    public function users()
    {
        // Get user statistics by role
        $usersByRole = User::with('roles')
            ->get()
            ->groupBy(function($user) {
                return $user->roles->first() ? $user->roles->first()->name : 'No Role';
            })
            ->map->count();
        
        // Get registration trends
        $registrationTrends = User::select(
            DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
            DB::raw('COUNT(*) as count')
        )
        ->where('created_at', '>=', now()->subMonths(12))
        ->groupBy('month')
        ->orderBy('month')
        ->get();
        
        // Get top volunteers by hours
        $topVolunteers = User::orderBy('total_volunteer_hours', 'desc')
            ->take(10)
            ->get();
            
        // Calculate totals
        $totalUsersByRole = array_sum($usersByRole->toArray());
        $maxRegistrationTrend = $registrationTrends->max('count') ?? 1;
        
        return view('admin.reports.users', compact('usersByRole', 'registrationTrends', 'topVolunteers', 'totalUsersByRole', 'maxRegistrationTrend'));
    }

    /**
     * Display event reports.
     */
    public function events()
    {
        // Get event statistics by status
        $eventsByStatus = Event::select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->get()
            ->pluck('count', 'status');
        
        // Get event statistics by category
        $eventsByCategory = Event::select('category', DB::raw('COUNT(*) as count'))
            ->whereNotNull('category')
            ->groupBy('category')
            ->get()
            ->pluck('count', 'category');
        
        // Get event trends
        $eventTrends = Event::select(
            DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
            DB::raw('COUNT(*) as count')
        )
        ->where('created_at', '>=', now()->subMonths(12))
        ->groupBy('month')
        ->orderBy('month')
        ->get();
        
        // Get popular events by applications
        $popularEvents = Event::withCount('applications')
            ->orderBy('applications_count', 'desc')
            ->take(10)
            ->get();
            
        // Calculate max values
        $maxEventsByStatus = $eventsByStatus->max() ?? 1;
        $maxEventsByCategory = $eventsByCategory->max() ?? 1;
        $maxEventTrend = $eventTrends->max('count') ?? 1;
        
        return view('admin.reports.events', compact('eventsByStatus', 'eventsByCategory', 'eventTrends', 'popularEvents', 'maxEventsByStatus', 'maxEventsByCategory', 'maxEventTrend'));
    }

    /**
     * Display organization reports.
     */
    public function organizations()
    {
        // Get organization statistics by status
        $orgsByStatus = Organization::select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->get()
            ->pluck('count', 'status');
        
        // Get organization statistics by verification
        $orgsByVerification = Organization::select(DB::raw('CASE WHEN is_verified = 1 THEN "Verified" ELSE "Unverified" END as verification'), DB::raw('COUNT(*) as count'))
            ->groupBy('verification')
            ->get()
            ->pluck('count', 'verification');
        
        // Get organization trends
        $orgTrends = Organization::select(
            DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
            DB::raw('COUNT(*) as count')
        )
        ->where('created_at', '>=', now()->subMonths(12))
        ->groupBy('month')
        ->orderBy('month')
        ->get();
        
        // Get top organizations by events
        $topOrganizations = Organization::withCount('events')
            ->orderBy('events_count', 'desc')
            ->take(10)
            ->get();
            
        // Calculate max values
        $maxOrgsByStatus = $orgsByStatus->max() ?? 1;
        $maxOrgsByVerification = $orgsByVerification->max() ?? 1;
        $maxOrgTrend = $orgTrends->max('count') ?? 1;
        
        return view('admin.reports.organizations', compact('orgsByStatus', 'orgsByVerification', 'orgTrends', 'topOrganizations', 'maxOrgsByStatus', 'maxOrgsByVerification', 'maxOrgTrend'));
    }
}