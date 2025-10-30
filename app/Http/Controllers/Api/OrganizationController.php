<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use Illuminate\Http\Request;

class OrganizationController extends Controller
{
    /**
     * Display a listing of verified organizations.
     */
    public function index(Request $request)
    {
        $query = Organization::where('status', 'approved')
            ->with(['users', 'events' => function($query) {
                $query->where('status', 'published')
                      ->where('start_date', '>=', now());
            }])
            ->withCount(['events as active_events_count' => function($query) {
                $query->where('status', 'published')
                      ->where('start_date', '>=', now());
            }]);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('mission_statement', 'like', "%{$search}%");
            });
        }

        // Filter by focus area
        if ($request->filled('focus_area')) {
            $focusArea = $request->get('focus_area');
            $query->whereJsonContains('focus_areas', $focusArea);
        }

        // Sort options
        $sortBy = $request->get('sort', 'name');
        switch ($sortBy) {
            case 'newest':
                $query->orderBy('created_at', 'desc');
                break;
            case 'most_active':
                $query->orderBy('active_events_count', 'desc');
                break;
            case 'name':
            default:
                $query->orderBy('name', 'asc');
                break;
        }

        $organizations = $query->paginate(12);

        return response()->json([
            'success' => true,
            'data' => $organizations,
            'message' => 'Organizations retrieved successfully'
        ]);
    }

    /**
     * Display the specified organization.
     */
    public function show(Organization $organization)
    {
        // Only show approved organizations
        if ($organization->status !== 'approved') {
            return response()->json([
                'success' => false,
                'message' => 'Organization not found'
            ], 404);
        }

        $organization->load([
            'users',
            'events' => function($query) {
                $query->where('status', 'published')
                      ->orderBy('start_date', 'asc');
            }
        ]);

        // Get upcoming and past events
        $upcomingEvents = $organization->events->where('start_date', '>=', now())->values();
        $pastEvents = $organization->events->where('end_date', '<', now())->values();

        // Get organization statistics
        $stats = [
            'total_events' => $organization->events->count(),
            'upcoming_events' => $upcomingEvents->count(),
            'completed_events' => $pastEvents->count(),
            'total_volunteers' => $organization->events->sum(function($event) {
                return $event->applications()->where('status', 'approved')->count();
            })
        ];

        return response()->json([
            'success' => true,
            'data' => [
                'organization' => $organization,
                'upcoming_events' => $upcomingEvents,
                'past_events' => $pastEvents,
                'stats' => $stats
            ],
            'message' => 'Organization retrieved successfully'
        ]);
    }
}