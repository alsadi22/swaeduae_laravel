<?php

namespace App\Http\Controllers\Organization;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Event;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $organization = $user->organization;
        
        if (!$organization) {
            return redirect()->route('organization.profile.show')
                ->with('error', 'Please complete your organization profile first.');
        }

        // Get organization events
        $events = Event::where('organization_id', $organization->id)->get();
        $eventIds = $events->pluck('id');

        // Application statistics
        $totalApplications = Application::whereIn('event_id', $eventIds)->count();
        $pendingApplications = Application::whereIn('event_id', $eventIds)
            ->where('status', 'pending')->count();
        $approvedApplications = Application::whereIn('event_id', $eventIds)
            ->where('status', 'approved')->count();
        $rejectedApplications = Application::whereIn('event_id', $eventIds)
            ->where('status', 'rejected')->count();

        // Event statistics
        $totalEvents = $events->count();
        $activeEvents = $events->where('status', 'published')
            ->where('end_date', '>=', now())->count();
        $completedEvents = $events->where('end_date', '<', now())->count();
        $draftEvents = $events->where('status', 'draft')->count();

        // Recent applications (last 10)
        $recentApplications = Application::with(['user', 'event'])
            ->whereIn('event_id', $eventIds)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Upcoming events
        $upcomingEvents = $events->where('status', 'published')
            ->where('start_date', '>=', now())
            ->sortBy('start_date')
            ->take(5);

        // Monthly application trends (last 6 months)
        $monthlyTrends = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $count = Application::whereIn('event_id', $eventIds)
                ->whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->count();
            
            $monthlyTrends[] = [
                'month' => $month->format('M Y'),
                'count' => $count
            ];
        }

        // Top performing events (by application count)
        $topEvents = Event::withCount(['applications'])
            ->where('organization_id', $organization->id)
            ->orderBy('applications_count', 'desc')
            ->limit(5)
            ->get();

        return view('organization.dashboard.index', compact(
            'organization',
            'totalApplications',
            'pendingApplications',
            'approvedApplications',
            'rejectedApplications',
            'totalEvents',
            'activeEvents',
            'completedEvents',
            'draftEvents',
            'recentApplications',
            'upcomingEvents',
            'monthlyTrends',
            'topEvents'
        ));
    }
}
