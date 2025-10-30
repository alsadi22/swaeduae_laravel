<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Application;
use App\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class EventController extends Controller
{
    /**
     * Get list of events
     */
    public function index(Request $request): JsonResponse
    {
        $query = Event::with(['organization', 'category'])
            ->where('status', 'approved')
            ->where('start_date', '>=', now()->toDateString());

        // Apply filters
        if ($request->category_id) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->location) {
            $query->where('location', 'like', '%' . $request->location . '%');
        }

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->start_date) {
            $query->where('start_date', '>=', $request->start_date);
        }

        if ($request->end_date) {
            $query->where('start_date', '<=', $request->end_date);
        }

        // Sort options
        $sortBy = $request->sort_by ?? 'start_date';
        $sortOrder = $request->sort_order ?? 'asc';
        
        if (in_array($sortBy, ['start_date', 'created_at', 'title'])) {
            $query->orderBy($sortBy, $sortOrder);
        }

        $events = $query->paginate($request->per_page ?? 15);

        // Add application status for authenticated users
        if (Auth::check()) {
            $events->getCollection()->transform(function ($event) {
                $application = Application::where('event_id', $event->id)
                    ->where('user_id', Auth::id())
                    ->first();
                
                $event->user_application_status = $application ? $application->status : null;
                $event->user_has_applied = (bool) $application;
                
                return $event;
            });
        }

        return response()->json([
            'success' => true,
            'data' => [
                'events' => $events
            ]
        ]);
    }

    /**
     * Get event details
     */
    public function show(Event $event): JsonResponse
    {
        $event->load(['organization', 'category', 'applications' => function ($query) {
            $query->where('status', 'approved');
        }]);

        // Add application status for authenticated users
        if (Auth::check()) {
            $application = Application::where('event_id', $event->id)
                ->where('user_id', Auth::id())
                ->first();
            
            $event->user_application_status = $application ? $application->status : null;
            $event->user_has_applied = (bool) $application;
            $event->user_application = $application;
        }

        // Add statistics
        $event->stats = [
            'total_applications' => $event->applications()->count(),
            'approved_applications' => $event->applications()->where('status', 'approved')->count(),
            'available_spots' => max(0, $event->max_volunteers - $event->applications()->where('status', 'approved')->count())
        ];

        return response()->json([
            'success' => true,
            'data' => [
                'event' => $event
            ]
        ]);
    }

    /**
     * Create new event (for organizations)
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'start_date' => 'required|date|after:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'location' => 'required|string|max:255',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'max_volunteers' => 'required|integer|min:1|max:1000',
            'skills_required' => 'nullable|array',
            'requirements' => 'nullable|string',
            'benefits' => 'nullable|string',
            'contact_email' => 'nullable|email',
            'contact_phone' => 'nullable|string|max:20',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid input data',
                'errors' => $validator->errors()
            ], 422);
        }

        // Check if user belongs to an organization
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $organization = $user->organizations()->first();
        if (!$organization) {
            return response()->json([
                'success' => false,
                'message' => 'You must be part of an organization to create events'
            ], 403);
        }

        $eventData = $request->except(['image']);
        $eventData['organization_id'] = $organization->id;
        $eventData['created_by'] = Auth::id();
        $eventData['status'] = 'pending'; // Requires admin approval

        // Handle image upload
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('events', 'public');
            $eventData['image'] = $imagePath;
        }

        $event = Event::create($eventData);

        return response()->json([
            'success' => true,
            'message' => 'Event created successfully and is pending approval',
            'data' => [
                'event' => $event->load(['organization', 'category'])
            ]
        ], 201);
    }

    /**
     * Update event (for organizations)
     */
    public function update(Request $request, Event $event): JsonResponse
    {
        // Check if user belongs to the event's organization
        if (!Auth::user()->organizations->contains($event->organization_id)) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string',
            'category_id' => 'sometimes|required|exists:categories,id',
            'start_date' => 'sometimes|required|date|after:today',
            'end_date' => 'sometimes|required|date|after_or_equal:start_date',
            'start_time' => 'sometimes|required|date_format:H:i',
            'end_time' => 'sometimes|required|date_format:H:i|after:start_time',
            'location' => 'sometimes|required|string|max:255',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'max_volunteers' => 'sometimes|required|integer|min:1|max:1000',
            'skills_required' => 'nullable|array',
            'requirements' => 'nullable|string',
            'benefits' => 'nullable|string',
            'contact_email' => 'nullable|email',
            'contact_phone' => 'nullable|string|max:20',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid input data',
                'errors' => $validator->errors()
            ], 422);
        }

        $updateData = $request->except(['image']);

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image
            if ($event->image) {
                Storage::disk('public')->delete($event->image);
            }
            
            $imagePath = $request->file('image')->store('events', 'public');
            $updateData['image'] = $imagePath;
        }

        $event->update($updateData);

        return response()->json([
            'success' => true,
            'message' => 'Event updated successfully',
            'data' => [
                'event' => $event->fresh()->load(['organization', 'category'])
            ]
        ]);
    }

    /**
     * Delete event (for organizations)
     */
    public function destroy(Event $event): JsonResponse
    {
        // Check if user belongs to the event's organization
        if (!Auth::user()->organizations->contains($event->organization_id)) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        // Check if event has applications
        if ($event->applications()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete event with existing applications'
            ], 400);
        }

        // Delete image if exists
        if ($event->image) {
            Storage::disk('public')->delete($event->image);
        }

        $event->delete();

        return response()->json([
            'success' => true,
            'message' => 'Event deleted successfully'
        ]);
    }
}