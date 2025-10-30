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

class DashboardController extends Controller
{
    public function index()
    {
        // Get statistics
        $totalUsers = User::count();
        $totalOrganizations = Organization::count();
        $totalEvents = Event::count();
        $totalApplications = Application::count();
        $totalCertificates = Certificate::count();
        
        // Get pending items that need admin attention
        $pendingOrganizations = Organization::where('status', 'pending')->count();
        $pendingEvents = Event::where('status', 'pending')->count();
        
        // Get recent activity
        $recentUsers = User::latest()->take(5)->get();
        $recentOrganizations = Organization::with('users')->latest()->take(5)->get();
        $recentEvents = Event::with('organization')->latest()->take(5)->get();
        
        // Get monthly trends for the last 6 months (database-agnostic)
        $driver = DB::connection()->getDriverName();
        
        if ($driver === 'pgsql') {
            $dateFormat = "TO_CHAR(created_at, 'YYYY-MM')";
        } elseif ($driver === 'mysql') {
            $dateFormat = "DATE_FORMAT(created_at, '%Y-%m')";
        } else {
            // SQLite and others
            $dateFormat = "strftime('%Y-%m', created_at)";
        }
        
        $monthlyUserTrends = User::select(
            DB::raw("{$dateFormat} as month"),
            DB::raw('COUNT(*) as count')
        )
        ->where('created_at', '>=', now()->subMonths(6))
        ->groupBy('month')
        ->orderBy('month')
        ->get();
        
        $monthlyEventTrends = Event::select(
            DB::raw("{$dateFormat} as month"),
            DB::raw('COUNT(*) as count')
        )
        ->where('created_at', '>=', now()->subMonths(6))
        ->groupBy('month')
        ->orderBy('month')
        ->get();
        
        // Calculate max values for trends
        $maxUserTrend = $monthlyUserTrends->max('count') ?? 1;
        $maxEventTrend = $monthlyEventTrends->max('count') ?? 1;
        
        return view('admin.dashboard.index', compact(
            'totalUsers',
            'totalOrganizations', 
            'totalEvents',
            'totalApplications',
            'totalCertificates',
            'pendingOrganizations',
            'pendingEvents',
            'recentUsers',
            'recentOrganizations',
            'recentEvents',
            'monthlyUserTrends',
            'monthlyEventTrends',
            'maxUserTrend',
            'maxEventTrend'
        ));
    }
}