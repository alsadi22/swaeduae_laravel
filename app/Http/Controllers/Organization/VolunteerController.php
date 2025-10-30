<?php

namespace App\Http\Controllers\Organization;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Event;
use App\Models\User;
use App\Mail\ApplicationStatusChanged;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class VolunteerController extends Controller
{
    /**
     * Display a listing of volunteer applications for the organization
     */
    public function index(Request $request)
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

        // Build query for applications
        $query = Application::with(['user', 'event'])
            ->whereIn('event_id', $eventIds);

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('event_id')) {
            $query->where('event_id', $request->event_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Apply sorting
        $sortBy = $request->get('sort', 'created_at');
        $sortOrder = $request->get('order', 'desc');
        
        if ($sortBy === 'volunteer_name') {
            $query->join('users', 'applications.user_id', '=', 'users.id')
                  ->orderBy('users.name', $sortOrder)
                  ->select('applications.*');
        } elseif ($sortBy === 'event_name') {
            $query->join('events', 'applications.event_id', '=', 'events.id')
                  ->orderBy('events.title', $sortOrder)
                  ->select('applications.*');
        } else {
            $query->orderBy($sortBy, $sortOrder);
        }

        $applications = $query->paginate(15)->withQueryString();

        // Get statistics for the filter bar
        $stats = [
            'total' => Application::whereIn('event_id', $eventIds)->count(),
            'pending' => Application::whereIn('event_id', $eventIds)->where('status', 'pending')->count(),
            'approved' => Application::whereIn('event_id', $eventIds)->where('status', 'approved')->count(),
            'rejected' => Application::whereIn('event_id', $eventIds)->where('status', 'rejected')->count(),
        ];

        return view('organization.volunteers.index', compact(
            'applications',
            'events',
            'stats'
        ));
    }

    /**
     * Display the specified volunteer application
     */
    public function show(Application $volunteer)
    {
        $user = Auth::user();
        $organization = $user->organization;
        
        // Check if this application belongs to the organization
        if ($volunteer->event->organization_id !== $organization->id) {
            abort(403, 'Unauthorized access to this application.');
        }

        $volunteer->load(['user', 'event']);

        return view('organization.volunteers.show', compact('volunteer'));
    }

    /**
     * Approve a volunteer application
     */
    public function approve(Request $request, Application $volunteer)
    {
        $user = Auth::user();
        $organization = $user->organization;
        
        // Check if this application belongs to the organization
        if ($volunteer->event->organization_id !== $organization->id) {
            abort(403, 'Unauthorized access to this application.');
        }

        // Check if application is still pending
        if ($volunteer->status !== 'pending') {
            return back()->with('error', 'This application has already been processed.');
        }

        // Check if event still has available spots
        $event = $volunteer->event;
        $approvedCount = $event->applications()->where('status', 'approved')->count();
        
        if ($approvedCount >= $event->max_volunteers) {
            return back()->with('error', 'This event has reached its maximum volunteer capacity.');
        }

        // Update application status
        $previousStatus = $volunteer->status;
        $volunteer->update([
            'status' => 'approved',
            'reviewed_at' => now(),
            'reviewed_by' => $user->id,
            'review_notes' => $request->input('notes')
        ]);

        // Send email notification
        try {
            Mail::to($volunteer->user->email)->send(new ApplicationStatusChanged($volunteer, $previousStatus));
        } catch (\Exception $e) {
            // Log the error but don't fail the approval process
            Log::error('Failed to send application approval email: ' . $e->getMessage());
        }

        return back()->with('success', 'Application approved successfully! The volunteer has been notified via email.');
    }

    /**
     * Reject a volunteer application
     */
    public function reject(Request $request, Application $volunteer)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:500'
        ]);

        $user = Auth::user();
        $organization = $user->organization;
        
        // Check if this application belongs to the organization
        if ($volunteer->event->organization_id !== $organization->id) {
            abort(403, 'Unauthorized access to this application.');
        }

        // Check if application is still pending
        if ($volunteer->status !== 'pending') {
            return back()->with('error', 'This application has already been processed.');
        }

        // Update application status
        $previousStatus = $volunteer->status;
        $volunteer->update([
            'status' => 'rejected',
            'reviewed_at' => now(),
            'reviewed_by' => $user->id,
            'review_notes' => $request->input('rejection_reason')
        ]);

        // Send email notification
        try {
            Mail::to($volunteer->user->email)->send(new ApplicationStatusChanged($volunteer, $previousStatus));
        } catch (\Exception $e) {
            // Log the error but don't fail the rejection process
            Log::error('Failed to send application rejection email: ' . $e->getMessage());
        }

        return back()->with('success', 'Application rejected. The volunteer has been notified via email.');
    }

    /**
     * Bulk approve/reject applications
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:approve,reject',
            'application_ids' => 'required|array',
            'application_ids.*' => 'exists:applications,id',
            'rejection_reason' => 'required_if:action,reject|string|max:500'
        ]);

        $user = Auth::user();
        $organization = $user->organization;
        $eventIds = Event::where('organization_id', $organization->id)->pluck('id');

        $applications = Application::whereIn('id', $request->application_ids)
            ->whereIn('event_id', $eventIds)
            ->where('status', 'pending')
            ->get();

        if ($applications->isEmpty()) {
            return back()->with('error', 'No valid applications found for bulk action.');
        }

        $action = $request->action;
        $count = 0;
        $emailErrors = 0;

        foreach ($applications as $application) {
            $previousStatus = $application->status;
            
            if ($action === 'approve') {
                // Check event capacity
                $event = $application->event;
                $approvedCount = $event->applications()->where('status', 'approved')->count();
                
                if ($approvedCount < $event->max_volunteers) {
                    $application->update([
                        'status' => 'approved',
                        'reviewed_at' => now(),
                        'reviewed_by' => $user->id
                    ]);
                    
                    // Send email notification
                    try {
                        Mail::to($application->user->email)->send(new ApplicationStatusChanged($application, $previousStatus));
                    } catch (\Exception $e) {
                        $emailErrors++;
                        Log::error('Failed to send bulk application email: ' . $e->getMessage());
                    }
                    
                    $count++;
                }
            } else {
                $application->update([
                    'status' => 'rejected',
                    'reviewed_at' => now(),
                    'reviewed_by' => $user->id,
                    'review_notes' => $request->rejection_reason
                ]);
                
                // Send email notification
                try {
                    Mail::to($application->user->email)->send(new ApplicationStatusChanged($application, $previousStatus));
                } catch (\Exception $e) {
                    $emailErrors++;
                    Log::error('Failed to send bulk application email: ' . $e->getMessage());
                }
                
                $count++;
            }
        }

        $actionText = $action === 'approve' ? 'approved' : 'rejected';
        $message = "{$count} applications {$actionText} successfully!";
        
        if ($emailErrors > 0) {
            $message .= " However, {$emailErrors} email notifications failed to send.";
        } else {
            $message .= " All volunteers have been notified via email.";
        }
        
        return back()->with('success', $message);
    }
}
