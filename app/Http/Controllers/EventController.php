<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class EventController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except(['index', 'show']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Event::with(['organization', 'applications']);

        // Filter by status
        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        // Filter by category
        if ($request->has('category') && $request->category !== '') {
            $query->where('category', $request->category);
        }

        // Filter by emirate
        if ($request->has('emirate') && $request->emirate !== '') {
            $query->where('emirate', $request->emirate);
        }

        // Search by title or description
        if ($request->has('search') && $request->search !== '') {
            $query->where(function($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        // Filter by organization for organization managers
        if (Auth::check() && Auth::user()?->hasRole(['organization-manager', 'organization-staff'])) {
            $userOrganizations = Auth::user()->organizations->pluck('id');
            $query->whereIn('organization_id', $userOrganizations);
        }

        // Only show published events for volunteers and guests
        if (!Auth::check() || (Auth::check() && Auth::user()?->hasRole('volunteer'))) {
            $query->where('status', 'published');
        }

        $events = $query->orderBy('start_date', 'desc')->paginate(15);

        return view('events.index', compact('events'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create', Event::class);

        $organizations = collect();
        
        if (Auth::user()->hasRole(['super-admin', 'admin'])) {
            $organizations = Organization::verified()->get();
        } elseif (Auth::user()->hasRole(['organization-manager', 'organization-staff'])) {
            $organizations = Auth::user()->organizations()->verified()->get();
        }

        return view('events.create', compact('organizations'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create', Event::class);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'requirements' => 'nullable|string',
            'organization_id' => 'required|exists:organizations,id',
            'category' => 'required|string|max:100',
            'tags' => 'nullable|array',
            'start_date' => 'required|date|after:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'location' => 'required|string|max:255',
            'address' => 'required|string',
            'city' => 'required|string|max:100',
            'emirate' => 'required|string|max:100',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'max_volunteers' => 'required|integer|min:1',
            'min_age' => 'nullable|integer|min:16|max:100',
            'max_age' => 'nullable|integer|min:16|max:100|gte:min_age',
            'skills_required' => 'nullable|array',
            'volunteer_hours' => 'required|numeric|min:0.5',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'gallery' => 'nullable|array',
            'gallery.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'requires_application' => 'boolean',
            'application_deadline' => 'nullable|date|before:start_date',
            'contact_person' => 'required|string|max:255',
            'contact_email' => 'required|email',
            'contact_phone' => 'required|string|max:20',
            'custom_fields' => 'nullable|array',
        ]);

        // Generate slug
        $validated['slug'] = Str::slug($validated['title']);
        
        // Ensure unique slug
        $originalSlug = $validated['slug'];
        $counter = 1;
        while (Event::where('slug', $validated['slug'])->exists()) {
            $validated['slug'] = $originalSlug . '-' . $counter;
            $counter++;
        }

        // Handle image upload
        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('events', 'public');
        }

        // Handle gallery upload
        if ($request->hasFile('gallery')) {
            $galleryPaths = [];
            foreach ($request->file('gallery') as $file) {
                $galleryPaths[] = $file->store('events/gallery', 'public');
            }
            $validated['gallery'] = $galleryPaths;
        }

        // Set status based on user role
        if (Auth::user()->hasRole(['super-admin', 'admin'])) {
            $validated['status'] = 'published';
            $validated['approved_at'] = now();
            $validated['approved_by'] = Auth::id();
        } else {
            $validated['status'] = 'pending';
        }

        $event = Event::create($validated);

        return redirect()->route('events.show', $event)
                        ->with('success', 'Event created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Event $event)
    {
        $event->load(['organization', 'applications.user', 'certificates']);

        // Check if user can view this event
        if ($event->status !== 'published' && !Auth::user()->can('view', $event)) {
            abort(403);
        }

        $userApplication = null;
        if (Auth::user()->hasRole('volunteer')) {
            $userApplication = $event->applications()
                                   ->where('user_id', Auth::id())
                                   ->first();
        }

        return view('events.show', compact('event', 'userApplication'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Event $event)
    {
        $this->authorize('update', $event);

        $organizations = collect();
        
        if (Auth::user()->hasRole(['super-admin', 'admin'])) {
            $organizations = Organization::verified()->get();
        } elseif (Auth::user()->hasRole(['organization-manager', 'organization-staff'])) {
            $organizations = Auth::user()->organizations()->verified()->get();
        }

        return view('events.edit', compact('event', 'organizations'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Event $event)
    {
        $this->authorize('update', $event);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'requirements' => 'nullable|string',
            'organization_id' => 'required|exists:organizations,id',
            'category' => 'required|string|max:100',
            'tags' => 'nullable|array',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'location' => 'required|string|max:255',
            'address' => 'required|string',
            'city' => 'required|string|max:100',
            'emirate' => 'required|string|max:100',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'max_volunteers' => 'required|integer|min:1',
            'min_age' => 'nullable|integer|min:16|max:100',
            'max_age' => 'nullable|integer|min:16|max:100|gte:min_age',
            'skills_required' => 'nullable|array',
            'volunteer_hours' => 'required|numeric|min:0.5',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'gallery' => 'nullable|array',
            'gallery.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'requires_application' => 'boolean',
            'application_deadline' => 'nullable|date|before:start_date',
            'contact_person' => 'required|string|max:255',
            'contact_email' => 'required|email',
            'contact_phone' => 'required|string|max:20',
            'custom_fields' => 'nullable|array',
        ]);

        // Update slug if title changed
        if ($validated['title'] !== $event->title) {
            $validated['slug'] = Str::slug($validated['title']);
            
            // Ensure unique slug
            $originalSlug = $validated['slug'];
            $counter = 1;
            while (Event::where('slug', $validated['slug'])->where('id', '!=', $event->id)->exists()) {
                $validated['slug'] = $originalSlug . '-' . $counter;
                $counter++;
            }
        }

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image
            if ($event->image) {
                Storage::disk('public')->delete($event->image);
            }
            $validated['image'] = $request->file('image')->store('events', 'public');
        }

        // Handle gallery upload
        if ($request->hasFile('gallery')) {
            // Delete old gallery images
            if ($event->gallery) {
                foreach ($event->gallery as $imagePath) {
                    Storage::disk('public')->delete($imagePath);
                }
            }
            
            $galleryPaths = [];
            foreach ($request->file('gallery') as $file) {
                $galleryPaths[] = $file->store('events/gallery', 'public');
            }
            $validated['gallery'] = $galleryPaths;
        }

        $event->update($validated);

        return redirect()->route('events.show', $event)
                        ->with('success', 'Event updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Event $event)
    {
        $this->authorize('delete', $event);

        // Delete associated images
        if ($event->image) {
            Storage::disk('public')->delete($event->image);
        }
        
        if ($event->gallery) {
            foreach ($event->gallery as $imagePath) {
                Storage::disk('public')->delete($imagePath);
            }
        }

        $event->delete();

        return redirect()->route('events.index')
                        ->with('success', 'Event deleted successfully!');
    }

    /**
     * Approve an event.
     */
    public function approve(Event $event)
    {
        $this->authorize('approve', $event);

        $event->update([
            'status' => 'published',
            'approved_at' => now(),
            'approved_by' => Auth::id(),
            'rejection_reason' => null,
        ]);

        return back()->with('success', 'Event approved successfully!');
    }

    /**
     * Reject an event.
     */
    public function reject(Request $request, Event $event)
    {
        $this->authorize('reject', $event);

        $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        $event->update([
            'status' => 'rejected',
            'rejection_reason' => $request->rejection_reason,
            'approved_at' => null,
            'approved_by' => null,
        ]);

        return back()->with('success', 'Event rejected successfully!');
    }

    /**
     * Feature/unfeature an event.
     */
    public function toggleFeature(Event $event)
    {
        $this->authorize('feature', $event);

        $event->update([
            'is_featured' => !$event->is_featured,
        ]);

        $message = $event->is_featured ? 'Event featured successfully!' : 'Event unfeatured successfully!';
        
        return back()->with('success', $message);
    }
}
