<?php

namespace App\Http\Controllers\Volunteer;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Application;
use App\Models\Certificate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Display the volunteer dashboard.
     */
    public function index()
    {
        // Ensure user is authenticated
        if (!Auth::check()) {
            return redirect()->route('login');
        }
        
        $user = Auth::user();
        
        // Get volunteer statistics
        $stats = [
            'total_applications' => Application::where('user_id', $user->id)->count(),
            'approved_applications' => Application::where('user_id', $user->id)
                ->where('status', 'approved')->count(),
            'completed_events' => Application::where('user_id', $user->id)
                ->where('status', 'completed')->count(),
            'certificates_earned' => Certificate::where('user_id', $user->id)->count(),
            'total_volunteer_hours' => $user->total_volunteer_hours ?? 0,
        ];
        
        // Get recent applications
        $recentApplications = Application::with('event')
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        // Get upcoming events (approved applications)
        $upcomingEvents = Application::with('event')
            ->where('user_id', $user->id)
            ->where('status', 'approved')
            ->whereHas('event', function($query) {
                $query->where('start_date', '>', now());
            })
            ->orderBy('created_at', 'desc')
            ->limit(3)
            ->get();
        
        // Get recent certificates
        $recentCertificates = Certificate::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit(3)
            ->get();
        
        // Get featured events for discovery
        $featuredEvents = Event::where('status', 'published')
            ->where('start_date', '>', now())
            ->where('max_volunteers', '>', function($query) {
                $query->selectRaw('count(*)')
                    ->from('applications')
                    ->whereColumn('event_id', 'events.id')
                    ->where('status', 'approved');
            })
            ->orderBy('is_featured', 'desc')
            ->orderBy('start_date', 'asc')
            ->limit(6)
            ->get();
        
        return view('volunteer.dashboard.index', compact(
            'recentApplications', 
            'upcomingEvents', 
            'recentCertificates', 
            'featuredEvents'
        ) + [
            'totalApplications' => $stats['total_applications'],
            'approvedApplications' => $stats['approved_applications'],
            'totalHours' => $stats['total_volunteer_hours'],
            'totalCertificates' => $stats['certificates_earned']
        ]);
    }
}