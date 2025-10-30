<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class ApplicationController extends Controller
{
    /**
     * Display a listing of applications.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 15);
        $status = $request->get('status');
        $eventId = $request->get('event_id');
        $userId = $request->get('user_id');

        $applications = Application::with(['user', 'event.organization'])
            ->when($status, function ($query, $status) {
                return $query->where('status', $status);
            })
            ->when($eventId, function ($query, $eventId) {
                return $query->where('event_id', $eventId);
            })
            ->when($userId, function ($query, $userId) {
                return $query->where('user_id', $userId);
            })
            ->paginate($perPage);

        return response()->json($applications);
    }

    /**
     * Store a newly created application in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'event_id' => 'required|exists:events,id',
            'motivation' => 'required|string|max:1000',
            'skills' => 'array',
            'availability' => 'array',
            'experience' => 'string|max:1000',
            'custom_responses' => 'array',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Check if user already applied
        $existingApplication = Application::where('user_id', Auth::id())
            ->where('event_id', $request->event_id)
            ->first();

        if ($existingApplication) {
            return response()->json(['message' => 'You have already applied for this event'], 400);
        }

        // Check if event requires application and deadline hasn't passed
        $event = Event::findOrFail($request->event_id);
        
        if (!$event->requires_application) {
            return response()->json(['message' => 'This event does not require an application'], 400);
        }

        if ($event->application_deadline && $event->application_deadline < now()) {
            return response()->json(['message' => 'Application deadline has passed'], 400);
        }

        if ($event->is_full) {
            return response()->json(['message' => 'This event is full'], 400);
        }

        $application = Application::create(array_merge(
            $request->only([
                'event_id', 'motivation', 'skills', 'availability', 
                'experience', 'custom_responses'
            ]),
            ['user_id' => Auth::id()]
        ));

        return response()->json($application, 201);
    }

    /**
     * Display the specified application.
     *
     * @param  \App\Models\Application  $application
     * @return \Illuminate\Http\Response
     */
    public function show(Application $application)
    {
        $application->load(['user', 'event.organization', 'reviewer']);
        
        return response()->json($application);
    }

    /**
     * Update the specified application in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Application  $application
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Application $application)
    {
        // Only allow updates if application is pending
        if ($application->status !== 'pending') {
            return response()->json(['message' => 'Cannot update application that is not pending'], 400);
        }

        $validator = Validator::make($request->all(), [
            'motivation' => 'string|max:1000',
            'skills' => 'array',
            'availability' => 'array',
            'experience' => 'string|max:1000',
            'custom_responses' => 'array',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $application->update($request->only([
            'motivation', 'skills', 'availability', 'experience', 'custom_responses'
        ]));

        return response()->json($application);
    }

    /**
     * Remove the specified application from storage.
     *
     * @param  \App\Models\Application  $application
     * @return \Illuminate\Http\Response
     */
    public function destroy(Application $application)
    {
        // Only allow deletion if application is pending
        if ($application->status !== 'pending') {
            return response()->json(['message' => 'Cannot delete application that is not pending'], 400);
        }

        $application->delete();

        return response()->json(['message' => 'Application deleted successfully']);
    }

    /**
     * Approve an application.
     *
     * @param  \App\Models\Application  $application
     * @return \Illuminate\Http\Response
     */
    public function approve(Application $application)
    {
        // Check if user has permission to approve (organization admin)
        if (!Auth::user()->can('approve', $application)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $application->update([
            'status' => 'approved',
            'reviewed_at' => now(),
            'reviewed_by' => Auth::id(),
        ]);

        return response()->json($application);
    }

    /**
     * Reject an application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Application  $application
     * @return \Illuminate\Http\Response
     */
    public function reject(Request $request, Application $application)
    {
        // Check if user has permission to reject (organization admin)
        if (!Auth::user()->can('approve', $application)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validator = Validator::make($request->all(), [
            'rejection_reason' => 'required|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $application->update([
            'status' => 'rejected',
            'rejection_reason' => $request->rejection_reason,
            'reviewed_at' => now(),
            'reviewed_by' => Auth::id(),
        ]);

        return response()->json($application);
    }

    /**
     * Get applications for the authenticated user.
     *
     * @return \Illuminate\Http\Response
     */
    public function myApplications(Request $request)
    {
        $perPage = $request->get('per_page', 15);
        $status = $request->get('status');

        $applications = Auth::user()->applications()
            ->with(['event.organization'])
            ->when($status, function ($query, $status) {
                return $query->where('status', $status);
            })
            ->paginate($perPage);

        return response()->json($applications);
    }

    /**
     * Get applications for events managed by the authenticated organization user.
     *
     * @return \Illuminate\Http\Response
     */
    public function organizationApplications(Request $request)
    {
        $user = Auth::user();
        
        // Get organizations where user is admin/manager
        $organizations = $user->organizations()
            ->wherePivotIn('role', ['admin', 'manager'])
            ->pluck('organizations.id');

        if ($organizations->isEmpty()) {
            return response()->json(['data' => []]);
        }

        $perPage = $request->get('per_page', 15);
        $status = $request->get('status');
        $eventId = $request->get('event_id');

        $applications = Application::with(['user', 'event'])
            ->whereIn('event_id', function ($query) use ($organizations) {
                $query->select('id')
                    ->from('events')
                    ->whereIn('organization_id', $organizations);
            })
            ->when($status, function ($query, $status) {
                return $query->where('status', $status);
            })
            ->when($eventId, function ($query, $eventId) {
                return $query->where('event_id', $eventId);
            })
            ->paginate($perPage);

        return response()->json($applications);
    }
}