<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Application;
use App\Models\Attendance;
use App\Models\Certificate;
use App\Models\Badge;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MobileController extends Controller
{
    /**
     * Get dashboard data for mobile app
     *
     * @return \Illuminate\Http\Response
     */
    public function dashboard()
    {
        $user = Auth::user();
        
        // Get user statistics
        $stats = [
            'total_volunteer_hours' => $user->total_volunteer_hours ?? 0,
            'total_events_attended' => $user->total_events_attended ?? 0,
            'total_certificates' => $user->certificates()->count(),
            'total_badges' => $user->badges()->count(),
            'points' => $user->points ?? 0,
        ];
        
        // Get upcoming events user has applied to
        $upcomingEvents = $user->applications()
            ->whereHas('event', function ($query) {
                $query->where('start_date', '>=', now());
            })
            ->with(['event.organization'])
            ->where('status', 'approved')
            ->limit(5)
            ->get()
            ->map(function ($application) {
                return [
                    'id' => $application->event->id,
                    'title' => $application->event->title,
                    'organization' => $application->event->organization->name,
                    'start_date' => $application->event->start_date,
                    'location' => $application->event->city . ', ' . $application->event->emirate,
                ];
            });
            
        // Get recent certificates
        $recentCertificates = $user->certificates()
            ->with(['event'])
            ->latest()
            ->limit(3)
            ->get()
            ->map(function ($certificate) {
                return [
                    'id' => $certificate->id,
                    'title' => $certificate->title,
                    'event_title' => $certificate->event->title,
                    'issued_date' => $certificate->issued_date,
                ];
            });
            
        // Get recent badges
        $recentBadges = $user->badges()
            ->limit(3)
            ->get();
            
        return response()->json([
            'user' => [
                'name' => $user->name,
                'email' => $user->email,
                'avatar' => $user->avatar,
            ],
            'stats' => $stats,
            'upcoming_events' => $upcomingEvents,
            'recent_certificates' => $recentCertificates,
            'recent_badges' => $recentBadges,
        ]);
    }

    /**
     * Get events for mobile app with search and filtering
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function events(Request $request)
    {
        $perPage = $request->get('per_page', 10);
        $search = $request->get('search');
        $category = $request->get('category');
        $city = $request->get('city');
        $emirate = $request->get('emirate');
        $date = $request->get('date');
        
        $events = Event::with(['organization'])
            ->where('status', 'published')
            ->where('start_date', '>=', now())
            ->when($search, function ($query, $search) {
                return $query->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            })
            ->when($category, function ($query, $category) {
                return $query->where('category', $category);
            })
            ->when($city, function ($query, $city) {
                return $query->where('city', $city);
            })
            ->when($emirate, function ($query, $emirate) {
                return $query->where('emirate', $emirate);
            })
            ->when($date, function ($query, $date) {
                return $query->whereDate('start_date', $date);
            })
            ->orderBy('start_date')
            ->paginate($perPage);
            
        // Add application status to each event
        $events->getCollection()->transform(function ($event) {
            $event->is_applied = Auth::user()->applications()
                ->where('event_id', $event->id)
                ->exists();
                
            $event->application_status = Auth::user()->applications()
                ->where('event_id', $event->id)
                ->value('status');
                
            return $event;
        });
        
        return response()->json($events);
    }

    /**
     * Get user's applications
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function applications(Request $request)
    {
        $perPage = $request->get('per_page', 10);
        $status = $request->get('status');
        
        $applications = Auth::user()->applications()
            ->with(['event.organization'])
            ->when($status, function ($query, $status) {
                return $query->where('status', $status);
            })
            ->latest()
            ->paginate($perPage);
            
        return response()->json($applications);
    }

    /**
     * Get user's attendance records
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function attendance(Request $request)
    {
        $perPage = $request->get('per_page', 10);
        $eventId = $request->get('event_id');
        
        $attendance = Auth::user()->attendances()
            ->with(['event'])
            ->when($eventId, function ($query, $eventId) {
                return $query->where('event_id', $eventId);
            })
            ->latest()
            ->paginate($perPage);
            
        return response()->json($attendance);
    }

    /**
     * Check-in to an event
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function checkin(Request $request)
    {
        $request->validate([
            'event_id' => 'required|exists:events,id',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);
        
        $event = Event::findOrFail($request->event_id);
        
        // Check if user is approved for this event
        $application = Auth::user()->applications()
            ->where('event_id', $event->id)
            ->where('status', 'approved')
            ->first();
            
        if (!$application) {
            return response()->json(['message' => 'You are not approved for this event'], 403);
        }
        
        // Check if already checked in
        $existingAttendance = Attendance::where('user_id', Auth::id())
            ->where('event_id', $event->id)
            ->whereNull('checked_out_at')
            ->first();
            
        if ($existingAttendance) {
            return response()->json(['message' => 'You are already checked in'], 400);
        }
        
        // Create attendance record
        $attendance = Attendance::create([
            'user_id' => Auth::id(),
            'event_id' => $event->id,
            'application_id' => $application->id,
            'checked_in_at' => now(),
            'checkin_latitude' => $request->latitude,
            'checkin_longitude' => $request->longitude,
            'status' => 'checked_in',
        ]);
        
        return response()->json([
            'message' => 'Successfully checked in',
            'attendance' => $attendance,
        ]);
    }

    /**
     * Check-out from an event
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function checkout(Request $request)
    {
        $request->validate([
            'attendance_id' => 'required|exists:attendances,id,user_id,' . Auth::id(),
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);
        
        $attendance = Attendance::findOrFail($request->attendance_id);
        
        // Check if already checked out
        if ($attendance->checked_out_at) {
            return response()->json(['message' => 'You are already checked out'], 400);
        }
        
        // Update attendance record
        $attendance->update([
            'checked_out_at' => now(),
            'checkout_latitude' => $request->latitude,
            'checkout_longitude' => $request->longitude,
            'status' => 'checked_out',
            'actual_hours' => $attendance->calculateActualHours(),
        ]);
        
        return response()->json([
            'message' => 'Successfully checked out',
            'attendance' => $attendance,
        ]);
    }

    /**
     * Get user's certificates
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function certificates(Request $request)
    {
        $perPage = $request->get('per_page', 10);
        $type = $request->get('type');
        
        $certificates = Auth::user()->certificates()
            ->with(['event.organization'])
            ->when($type, function ($query, $type) {
                return $query->where('type', $type);
            })
            ->latest()
            ->paginate($perPage);
            
        return response()->json($certificates);
    }

    /**
     * Get user's badges
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function badges(Request $request)
    {
        $perPage = $request->get('per_page', 10);
        $type = $request->get('type');
        
        $badges = Auth::user()->badges()
            ->when($type, function ($query, $type) {
                return $query->where('type', $type);
            })
            ->paginate($perPage);
            
        return response()->json($badges);
    }

    /**
     * Get notifications for the user
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function notifications(Request $request)
    {
        $perPage = $request->get('per_page', 10);
        $unreadOnly = $request->get('unread_only', false);
        
        $query = Auth::user()->notifications();
        
        if ($unreadOnly) {
            $query->whereNull('read_at');
        }
        
        $notifications = $query->paginate($perPage);
        
        return response()->json($notifications);
    }

    /**
     * Mark notification as read
     *
     * @param  string  $id
     * @return \Illuminate\Http\Response
     */
    public function markNotificationAsRead($id)
    {
        $notification = Auth::user()->notifications()->where('id', $id)->first();
        
        if (!$notification) {
            return response()->json(['message' => 'Notification not found'], 404);
        }
        
        $notification->markAsRead();
        
        return response()->json(['message' => 'Notification marked as read']);
    }
}