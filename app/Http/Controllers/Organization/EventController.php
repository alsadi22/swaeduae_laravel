<?php

namespace App\Http\Controllers\Organization;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class EventController extends Controller
{
    /**
     * Display a listing of organization events.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $organization = $user->organization;
        
        if (!$organization) {
            return redirect()->route('organization.profile.show')
                ->with('error', 'Please complete your organization profile first.');
        }

        $query = Event::where('organization_id', $organization->id)
            ->with(['applications' => function($q) {
                $q->selectRaw('event_id, status, count(*) as count')
                  ->groupBy('event_id', 'status');
            }]);

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $events = $query->orderBy('created_at', 'desc')->paginate(12);

        // Get categories for filter
        $categories = Event::select('category')
            ->distinct()
            ->whereNotNull('category')
            ->where('organization_id', $organization->id)
            ->pluck('category');

        return view('organization.events.index', compact('events', 'categories'));
    }

    /**
     * Show the form for creating a new event (Step 1 of wizard).
     */
    public function create(Request $request)
    {
        $user = Auth::user();
        $organization = $user->organization;
        
        if (!$organization) {
            return redirect()->route('organization.profile.show')
                ->with('error', 'Please complete your organization profile first.');
        }

        $step = $request->get('step', 1);
        $eventData = session('event_wizard_data', []);

        // Categories for dropdown
        $categories = [
            'Environment', 'Education', 'Health', 'Community Service',
            'Elderly Care', 'Children & Youth', 'Disability Support',
            'Animal Welfare', 'Arts & Culture', 'Sports & Recreation',
            'Emergency Response', 'Food & Nutrition', 'Technology',
            'Women Empowerment', 'Other'
        ];

        // Emirates for dropdown
        $emirates = [
            'Abu Dhabi', 'Dubai', 'Sharjah', 'Ajman',
            'Umm Al Quwain', 'Ras Al Khaimah', 'Fujairah'
        ];

        // Skills options
        $skillsOptions = [
            'Communication', 'Leadership', 'Teaching', 'First Aid',
            'Event Management', 'Social Media', 'Photography',
            'Translation', 'Computer Skills', 'Driving',
            'Cooking', 'Childcare', 'Elderly Care', 'Manual Labor',
            'Administrative', 'Marketing', 'Design', 'Other'
        ];

        return view('organization.events.create', compact(
            'step', 'eventData', 'categories', 'emirates', 'skillsOptions', 'organization'
        ));
    }

    /**
     * Store event wizard step data.
     */
    public function storeStep(Request $request)
    {
        $step = $request->input('step', 1);
        $eventData = session('event_wizard_data', []);

        switch ($step) {
            case 1:
                $validated = $request->validate([
                    'title' => 'required|string|max:255',
                    'category' => 'required|string',
                    'description' => 'required|string|min:50',
                    'requirements' => 'nullable|string',
                ]);
                break;

            case 2:
                $validated = $request->validate([
                    'start_date' => 'required|date|after:today',
                    'end_date' => 'required|date|after_or_equal:start_date',
                    'start_time' => 'required|date_format:H:i',
                    'end_time' => 'required|date_format:H:i|after:start_time',
                    'location' => 'required|string|max:255',
                    'address' => 'required|string|max:500',
                    'city' => 'required|string|max:100',
                    'emirate' => 'required|string',
                ]);
                break;

            case 3:
                $validated = $request->validate([
                    'max_volunteers' => 'required|integer|min:1|max:1000',
                    'min_age' => 'nullable|integer|min:13|max:100',
                    'max_age' => 'nullable|integer|min:13|max:100|gte:min_age',
                    'skills_required' => 'nullable|array',
                    'volunteer_hours' => 'required|numeric|min:0.5|max:24',
                    'requires_application' => 'boolean',
                    'application_deadline' => 'nullable|date|before:start_date',
                ]);
                break;

            case 4:
                $validated = $request->validate([
                    'contact_person' => 'required|string|max:255',
                    'contact_email' => 'required|email|max:255',
                    'contact_phone' => 'required|string|max:20',
                    'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
                    'gallery.*' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
                    'tags' => 'nullable|string',
                ]);
                break;
        }

        // Merge validated data with existing session data
        $eventData = array_merge($eventData, $validated);
        session(['event_wizard_data' => $eventData]);

        // If this is the final step, create the event
        if ($step == 4) {
            return $this->finalizeEvent($request);
        }

        // Redirect to next step
        return redirect()->route('organization.events.create', ['step' => $step + 1])
            ->with('success', 'Step ' . $step . ' completed successfully!');
    }

    /**
     * Finalize and store the event.
     */
    private function finalizeEvent(Request $request)
    {
        $eventData = session('event_wizard_data', []);
        $user = Auth::user();
        $organization = $user->organization;

        DB::beginTransaction();
        try {
            // Handle image upload
            $imagePath = null;
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('events', 'public');
            }

            // Handle gallery images
            $galleryPaths = [];
            if ($request->hasFile('gallery')) {
                foreach ($request->file('gallery') as $file) {
                    $galleryPaths[] = $file->store('events/gallery', 'public');
                }
            }

            // Process tags
            $tags = [];
            if (!empty($eventData['tags'])) {
                $tags = array_map('trim', explode(',', $eventData['tags']));
            }

            // Create the event
            $event = Event::create([
                'title' => $eventData['title'],
                'slug' => Str::slug($eventData['title']) . '-' . time(),
                'description' => $eventData['description'],
                'requirements' => $eventData['requirements'] ?? null,
                'organization_id' => $organization->id,
                'category' => $eventData['category'],
                'tags' => $tags,
                'start_date' => $eventData['start_date'],
                'end_date' => $eventData['end_date'],
                'start_time' => $eventData['start_time'],
                'end_time' => $eventData['end_time'],
                'location' => $eventData['location'],
                'address' => $eventData['address'],
                'city' => $eventData['city'],
                'emirate' => $eventData['emirate'],
                'max_volunteers' => $eventData['max_volunteers'],
                'min_age' => $eventData['min_age'] ?? null,
                'max_age' => $eventData['max_age'] ?? null,
                'skills_required' => $eventData['skills_required'] ?? [],
                'volunteer_hours' => $eventData['volunteer_hours'],
                'image' => $imagePath,
                'gallery' => $galleryPaths,
                'status' => 'draft', // Events start as draft
                'requires_application' => $eventData['requires_application'] ?? true,
                'application_deadline' => $eventData['application_deadline'] ?? null,
                'contact_person' => $eventData['contact_person'],
                'contact_email' => $eventData['contact_email'],
                'contact_phone' => $eventData['contact_phone'],
            ]);

            DB::commit();

            // Clear wizard data
            session()->forget('event_wizard_data');

            return redirect()->route('organization.events.show', $event)
                ->with('success', 'Event created successfully! It will be reviewed by administrators before publication.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Failed to create event. Please try again.']);
        }
    }

    /**
     * Display the specified event.
     */
    public function show(Event $event)
    {
        $user = Auth::user();
        $organization = $user->organization;

        // Check if user can view this event
        if ($event->organization_id !== $organization->id) {
            abort(403, 'Unauthorized access to this event.');
        }

        $event->load(['applications.user', 'organization']);

        // Get application statistics
        $applicationStats = [
            'total' => $event->applications->count(),
            'pending' => $event->applications->where('status', 'pending')->count(),
            'approved' => $event->applications->where('status', 'approved')->count(),
            'rejected' => $event->applications->where('status', 'rejected')->count(),
        ];

        return view('organization.events.show', compact('event', 'applicationStats'));
    }

    /**
     * Show the form for editing the specified event.
     */
    public function edit(Event $event)
    {
        $user = Auth::user();
        $organization = $user->organization;

        // Check if user can edit this event
        if ($event->organization_id !== $organization->id) {
            abort(403, 'Unauthorized access to this event.');
        }

        // Categories and other options (same as create)
        $categories = [
            'Environment', 'Education', 'Health', 'Community Service',
            'Elderly Care', 'Children & Youth', 'Disability Support',
            'Animal Welfare', 'Arts & Culture', 'Sports & Recreation',
            'Emergency Response', 'Food & Nutrition', 'Technology',
            'Women Empowerment', 'Other'
        ];

        $emirates = [
            'Abu Dhabi', 'Dubai', 'Sharjah', 'Ajman',
            'Umm Al Quwain', 'Ras Al Khaimah', 'Fujairah'
        ];

        $skillsOptions = [
            'Communication', 'Leadership', 'Teaching', 'First Aid',
            'Event Management', 'Social Media', 'Photography',
            'Translation', 'Computer Skills', 'Driving',
            'Cooking', 'Childcare', 'Elderly Care', 'Manual Labor',
            'Administrative', 'Marketing', 'Design', 'Other'
        ];

        return view('organization.events.edit', compact(
            'event', 'categories', 'emirates', 'skillsOptions', 'organization'
        ));
    }

    /**
     * Update the specified event.
     */
    public function update(Request $request, Event $event)
    {
        $user = Auth::user();
        $organization = $user->organization;

        // Check if user can update this event
        if ($event->organization_id !== $organization->id) {
            abort(403, 'Unauthorized access to this event.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|string',
            'description' => 'required|string|min:50',
            'requirements' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i',
            'location' => 'required|string|max:255',
            'address' => 'required|string|max:500',
            'city' => 'required|string|max:100',
            'emirate' => 'required|string',
            'max_volunteers' => 'required|integer|min:1|max:1000',
            'min_age' => 'nullable|integer|min:13|max:100',
            'max_age' => 'nullable|integer|min:13|max:100|gte:min_age',
            'skills_required' => 'nullable|array',
            'volunteer_hours' => 'required|numeric|min:0.5|max:24',
            'requires_application' => 'boolean',
            'application_deadline' => 'nullable|date',
            'contact_person' => 'required|string|max:255',
            'contact_email' => 'required|email|max:255',
            'contact_phone' => 'required|string|max:20',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'tags' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            // Handle image upload
            if ($request->hasFile('image')) {
                // Delete old image
                if ($event->image) {
                    Storage::disk('public')->delete($event->image);
                }
                $validated['image'] = $request->file('image')->store('events', 'public');
            }

            // Process tags
            if (!empty($validated['tags'])) {
                $validated['tags'] = array_map('trim', explode(',', $validated['tags']));
            }

            // Update slug if title changed
            if ($validated['title'] !== $event->title) {
                $validated['slug'] = Str::slug($validated['title']) . '-' . time();
            }

            $event->update($validated);

            DB::commit();

            return redirect()->route('organization.events.show', $event)
                ->with('success', 'Event updated successfully!');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Failed to update event. Please try again.']);
        }
    }

    /**
     * Remove the specified event.
     */
    public function destroy(Event $event)
    {
        $user = Auth::user();
        $organization = $user->organization;

        // Check if user can delete this event
        if ($event->organization_id !== $organization->id) {
            abort(403, 'Unauthorized access to this event.');
        }

        // Check if event has applications
        if ($event->applications()->count() > 0) {
            return back()->withErrors(['error' => 'Cannot delete event with existing applications.']);
        }

        DB::beginTransaction();
        try {
            // Delete associated files
            if ($event->image) {
                Storage::disk('public')->delete($event->image);
            }
            if ($event->gallery) {
                foreach ($event->gallery as $image) {
                    Storage::disk('public')->delete($image);
                }
            }

            $event->delete();

            DB::commit();

            return redirect()->route('organization.events.index')
                ->with('success', 'Event deleted successfully!');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Failed to delete event. Please try again.']);
        }
    }

    /**
     * Publish an event.
     */
    public function publish(Event $event)
    {
        $user = Auth::user();
        $organization = $user->organization;

        if ($event->organization_id !== $organization->id) {
            abort(403, 'Unauthorized access to this event.');
        }

        $event->update(['status' => 'pending_approval']);

        return back()->with('success', 'Event submitted for approval!');
    }

    /**
     * Unpublish an event.
     */
    public function unpublish(Event $event)
    {
        $user = Auth::user();
        $organization = $user->organization;

        if ($event->organization_id !== $organization->id) {
            abort(403, 'Unauthorized access to this event.');
        }

        $event->update(['status' => 'draft']);

        return back()->with('success', 'Event unpublished successfully!');
    }
}
