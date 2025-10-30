<?php

namespace App\Http\Controllers\Volunteer;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EventController extends Controller
{
    /**
     * Display a listing of events for volunteers.
     */
    public function index(Request $request)
    {
        $query = Event::with(['organization'])
            ->where('status', 'published')
            ->where('start_date', '>', now());
        
        // Apply filters
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }
        
        if ($request->filled('location')) {
            $query->where('location', 'LIKE', '%' . $request->location . '%');
        }
        
        if ($request->filled('skills')) {
            $skills = explode(',', $request->skills);
            $query->where(function($q) use ($skills) {
                foreach ($skills as $skill) {
                    $q->orWhereJsonContains('required_skills', trim($skill));
                }
            });
        }
        
        if ($request->filled('date_from')) {
            $query->where('start_date', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->where('end_date', '<=', $request->date_to);
        }
        
        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'LIKE', '%' . $search . '%')
                  ->orWhere('description', 'LIKE', '%' . $search . '%')
                  ->orWhere('location', 'LIKE', '%' . $search . '%');
            });
        }
        
        // Add application status for current user
        $query->withCount(['applications as user_applied' => function($q) {
            $q->where('user_id', Auth::id());
        }]);
        
        $events = $query->orderBy('featured', 'desc')
            ->orderBy('start_date', 'asc')
            ->paginate(12);
        
        // Get categories for filter
        $categories = Event::select('category')
            ->distinct()
            ->whereNotNull('category')
            ->pluck('category');
        
        return view('volunteer.events.index', compact('events', 'categories'));
    }
    
    /**
     * Display the specified event.
     */
    public function show(Event $event)
    {
        $event->load(['organization', 'applications' => function($query) {
            $query->where('status', 'approved');
        }]);
        
        // Check if user has already applied
        $userApplication = null;
        if (Auth::check()) {
            $userApplication = Application::where('event_id', $event->id)
                ->where('user_id', Auth::id())
                ->first();
        }
        
        // Calculate available spots
        $approvedApplications = $event->applications->count();
        $availableSpots = $event->max_volunteers - $approvedApplications;
        
        // Get similar events
        $similarEvents = Event::where('id', '!=', $event->id)
            ->where('status', 'published')
            ->where('start_date', '>', now())
            ->where(function($query) use ($event) {
                $query->where('category', $event->category)
                      ->orWhere('location', 'LIKE', '%' . explode(',', $event->location)[0] . '%');
            })
            ->limit(3)
            ->get();
        
        return view('volunteer.events.show', compact(
            'event', 
            'userApplication', 
            'availableSpots', 
            'similarEvents'
        ));
    }

    /**
     * Show the application form for an event.
     */
    public function showApplicationForm(Event $event)
    {
        $event->load(['organization']);
        
        // Check if user already applied
        $existingApplication = Application::where('event_id', $event->id)
            ->where('user_id', Auth::id())
            ->first();
        
        if ($existingApplication) {
            return redirect()->route('volunteer.events.show', $event)
                ->with('error', 'You have already applied to this event.');
        }
        
        // Check if event is still accepting applications
        if (!$event->applications_open) {
            return redirect()->route('volunteer.events.show', $event)
                ->with('error', 'This event is no longer accepting applications.');
        }
        
        return view('volunteer.events.apply', compact('event'));
    }

    /**
     * Apply to volunteer for an event.
     */
    public function apply(Request $request, Event $event)
    {
        $validated = $request->validate([
            'motivation' => 'required|string|max:1000',
            'skills' => 'nullable|array',
            'skills.*' => 'string|max:100',
            'other_skills' => 'nullable|string|max:500',
            'availability' => 'required|string|max:500',
            'emergency_name' => 'required|string|max:100',
            'emergency_phone' => 'required|string|max:20',
            'custom_responses' => 'nullable|array',
            'custom_responses.*' => 'nullable|string|max:1000',
            'terms_accepted' => 'required|accepted',
        ]);
        
        // Check if user already applied
        $existingApplication = Application::where('event_id', $event->id)
            ->where('user_id', Auth::id())
            ->first();
        
        if ($existingApplication) {
            return back()->with('error', 'You have already applied to this event.');
        }
        
        // Check if event is still accepting applications
        if ($event->start_date <= now()) {
            return back()->with('error', 'This event is no longer accepting applications.');
        }
        
        // Check application deadline
        if ($event->application_deadline && $event->application_deadline < now()) {
            return back()->with('error', 'The application deadline for this event has passed.');
        }
        
        // Check if event has available spots
        $approvedApplications = Application::where('event_id', $event->id)
            ->where('status', 'approved')
            ->count();
        
        if ($approvedApplications >= $event->max_volunteers) {
            return back()->with('error', 'This event is full.');
        }
        
        // Process skills
        $skills = $validated['skills'] ?? [];
        if (!empty($validated['other_skills'])) {
            $otherSkills = array_map('trim', explode(',', $validated['other_skills']));
            $skills = array_merge($skills, $otherSkills);
        }
        
        // Prepare emergency contact
        $emergencyContact = [
            'name' => $validated['emergency_name'],
            'phone' => $validated['emergency_phone'],
        ];
        
        // Create application
        Application::create([
            'user_id' => Auth::id(),
            'event_id' => $event->id,
            'motivation' => $validated['motivation'],
            'skills' => $skills,
            'availability' => $validated['availability'],
            'custom_responses' => $validated['custom_responses'] ?? [],
            'emergency_contact' => $emergencyContact,
            'status' => 'pending',
            'applied_at' => now(),
        ]);
        
        return redirect()->route('volunteer.events.show', $event)
            ->with('success', 'Your application has been submitted successfully! You will be notified once it has been reviewed.');
    }
    
    /**
     * Withdraw application from an event.
     */
    public function withdraw(Event $event)
    {
        $application = Application::where('event_id', $event->id)
            ->where('user_id', Auth::id())
            ->first();
        
        if (!$application) {
            return back()->with('error', 'No application found for this event.');
        }
        
        if ($application->status === 'completed') {
            return back()->with('error', 'Cannot withdraw from a completed event.');
        }
        
        $application->delete();
        
        return back()->with('success', 'Your application has been withdrawn.');
    }
}
