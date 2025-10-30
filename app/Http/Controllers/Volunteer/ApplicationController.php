<?php

namespace App\Http\Controllers\Volunteer;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ApplicationController extends Controller
{
    /**
     * Display a listing of the user's applications.
     */
    public function index(Request $request)
    {
        $query = Application::with(['event.organization', 'event.category'])
            ->where('user_id', Auth::id());

        // Filter by status
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Sort applications
        switch ($request->get('sort', 'newest')) {
            case 'oldest':
                $query->orderBy('applied_at', 'asc');
                break;
            case 'event_date':
                $query->join('events', 'applications.event_id', '=', 'events.id')
                      ->orderBy('events.start_date', 'asc')
                      ->select('applications.*');
                break;
            case 'newest':
            default:
                $query->orderBy('applied_at', 'desc');
                break;
        }

        $applications = $query->paginate(10)->withQueryString();

        return view('volunteer.applications.index', compact('applications'));
    }

    /**
     * Display the specified application.
     */
    public function show(Application $application)
    {
        // Ensure user can only view their own applications
        if ($application->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to application.');
        }

        $application->load(['event.organization', 'reviewedBy']);

        return view('volunteer.applications.show', compact('application'));
    }

    /**
     * Update the specified application.
     */
    public function update(Request $request, Application $application)
    {
        // Ensure user can only update their own applications
        if ($application->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to application.');
        }

        // Only allow updates if application is still pending
        if ($application->status !== 'pending') {
            return redirect()->back()->with('error', 'Cannot update application that has already been reviewed.');
        }

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
        ]);

        // Process skills
        $skills = $validated['skills'] ?? [];
        if (!empty($validated['other_skills'])) {
            $otherSkills = array_map('trim', explode(',', $validated['other_skills']));
            $skills = array_merge($skills, $otherSkills);
        }

        // Prepare custom responses
        $customResponses = $validated['custom_responses'] ?? [];

        // Prepare emergency contact
        $emergencyContact = [
            'name' => $validated['emergency_name'],
            'phone' => $validated['emergency_phone'],
        ];

        DB::transaction(function () use ($application, $validated, $skills, $customResponses, $emergencyContact) {
            $application->update([
                'motivation' => $validated['motivation'],
                'skills' => $skills,
                'availability' => $validated['availability'],
                'custom_responses' => $customResponses,
                'emergency_contact' => $emergencyContact,
                'updated_at' => now(),
            ]);
        });

        return redirect()->route('volunteer.applications.show', $application)
            ->with('success', 'Application updated successfully!');
    }

    /**
     * Remove the specified application (withdraw).
     */
    public function destroy(Application $application)
    {
        // Ensure user can only delete their own applications
        if ($application->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to application.');
        }

        // Only allow withdrawal if application is pending or approved (not if already attended)
        if (in_array($application->status, ['rejected', 'completed']) || $application->attended) {
            return redirect()->back()->with('error', 'Cannot withdraw this application.');
        }

        DB::transaction(function () use ($application) {
            $application->delete();
        });

        return redirect()->route('volunteer.applications.index')
            ->with('success', 'Application withdrawn successfully.');
    }

    /**
     * Get applications by status for AJAX requests.
     */
    public function getByStatus(Request $request)
    {
        $status = $request->get('status', 'all');
        
        $query = Application::with(['event.organization'])
            ->where('user_id', Auth::id());

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $applications = $query->orderBy('created_at', 'desc')->get();

        return response()->json([
            'applications' => $applications->map(function ($application) {
                return [
                    'id' => $application->id,
                    'event_title' => $application->event->title,
                    'organization_name' => $application->event->organization->name,
                    'status' => $application->status,
                    'applied_at' => $application->applied_at->format('M j, Y'),
                    'event_date' => $application->event->start_date->format('M j, Y'),
                    'url' => route('volunteer.applications.show', $application),
                ];
            }),
        ]);
    }
}
