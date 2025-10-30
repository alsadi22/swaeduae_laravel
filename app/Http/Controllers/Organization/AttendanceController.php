<?php

namespace App\Http\Controllers\Organization;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Attendance;
use App\Models\Application;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class AttendanceController extends Controller
{
    /**
     * Display attendance records for organization events.
     */
    public function index(Request $request)
    {
        $organization = Auth::user()->organizations()->first();
        
        if (!$organization) {
            return redirect()->route('organization.dashboard')
                           ->with('error', 'You must be associated with an organization to access this page.');
        }

        $query = Attendance::with(['user', 'event', 'application'])
                          ->whereHas('event', function($q) use ($organization) {
                              $q->where('organization_id', $organization->id);
                          });

        // Filter by event
        if ($request->filled('event_id')) {
            $query->where('event_id', $request->event_id);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('checked_in_at', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->whereDate('checked_in_at', '<=', $request->date_to);
        }

        // Search by volunteer name
        if ($request->filled('search')) {
            $query->whereHas('user', function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        $attendances = $query->orderBy('checked_in_at', 'desc')->paginate(20);
        
        // Get organization events for filter dropdown
        $events = Event::where('organization_id', $organization->id)
                      ->orderBy('start_date', 'desc')
                      ->get(['id', 'title', 'start_date']);

        return view('organization.attendance.index', compact('attendances', 'events'));
    }

    /**
     * Show real-time attendance for a specific event.
     */
    public function realtime(Event $event)
    {
        $this->authorize('view', $event);

        $attendances = Attendance::with(['user', 'application'])
                                ->where('event_id', $event->id)
                                ->orderBy('checked_in_at', 'desc')
                                ->get();

        $stats = [
            'total_registered' => $event->approvedApplications()->count(),
            'checked_in' => $attendances->where('status', 'checked_in')->count(),
            'checked_out' => $attendances->where('status', 'checked_out')->count(),
            'no_show' => $event->approvedApplications()->count() - $attendances->count(),
            'total_hours' => $attendances->sum('actual_hours'),
            'average_hours' => $attendances->where('actual_hours', '>', 0)->avg('actual_hours'),
        ];

        return view('organization.attendance.realtime', compact('event', 'attendances', 'stats'));
    }

    /**
     * Show attendance details for a specific event.
     */
    public function show(Event $event)
    {
        $this->authorize('view', $event);

        $attendances = Attendance::with(['user', 'application'])
                                ->where('event_id', $event->id)
                                ->orderBy('checked_in_at', 'desc')
                                ->get();

        $stats = [
            'total_registered' => $event->approvedApplications()->count(),
            'checked_in' => $attendances->where('status', 'checked_in')->count(),
            'checked_out' => $attendances->where('status', 'checked_out')->count(),
            'no_show' => $event->approvedApplications()->count() - $attendances->count(),
            'total_hours' => $attendances->sum('actual_hours'),
            'average_hours' => $attendances->where('actual_hours', '>', 0)->avg('actual_hours'),
        ];

        return view('organization.attendance.show', compact('event', 'attendances', 'stats'));
    }

    /**
     * Generate QR codes for event check-in.
     */
    public function generateQrCodes(Event $event)
    {
        $this->authorize('update', $event);

        $approvedApplications = $event->approvedApplications()->with('user')->get();
        $qrCodes = [];

        foreach ($approvedApplications as $application) {
            $qrData = [
                'event_id' => $event->id,
                'user_id' => $application->user_id,
                'application_id' => $application->id,
                'token' => Str::random(32),
                'expires_at' => Carbon::now()->addDays(1)->timestamp,
            ];

            $qrString = base64_encode(json_encode($qrData));
            
            $qrCodes[] = [
                'user' => $application->user,
                'application' => $application,
                'qr_code' => QrCode::size(200)->generate($qrString),
                'qr_data' => $qrString,
            ];
        }

        return view('organization.attendance.qr-codes', compact('event', 'qrCodes'));
    }

    /**
     * Process QR code check-in.
     */
    public function checkin(Request $request)
    {
        $request->validate([
            'qr_data' => 'required|string',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'device_info' => 'nullable|string|max:500',
            'notes' => 'nullable|string|max:1000',
        ]);

        try {
            $qrData = json_decode(base64_decode($request->qr_data), true);
            
            if (!$qrData || !isset($qrData['event_id'], $qrData['user_id'], $qrData['application_id'])) {
                return response()->json(['error' => 'Invalid QR code data'], 400);
            }

            // Check if QR code has expired
            if (isset($qrData['expires_at']) && $qrData['expires_at'] < time()) {
                return response()->json(['error' => 'QR code has expired'], 400);
            }

            $event = Event::findOrFail($qrData['event_id']);
            $user = User::findOrFail($qrData['user_id']);
            $application = Application::findOrFail($qrData['application_id']);

            // Verify application is approved
            if ($application->status !== 'approved') {
                return response()->json(['error' => 'Application is not approved'], 400);
            }

            // Check if already checked in
            $existingAttendance = Attendance::where('event_id', $event->id)
                                          ->where('user_id', $user->id)
                                          ->first();

            if ($existingAttendance && $existingAttendance->checked_in_at) {
                return response()->json(['error' => 'Already checked in'], 400);
            }

            // Calculate distance from event location if GPS provided
            $distanceFromEvent = null;
            $isValidLocation = true;

            if ($request->latitude && $request->longitude && $event->latitude && $event->longitude) {
                $distanceFromEvent = $this->calculateDistance(
                    $request->latitude, 
                    $request->longitude,
                    $event->latitude, 
                    $event->longitude
                );

                // Consider valid if within 500 meters
                $isValidLocation = $distanceFromEvent <= 500;
            }

            // Create or update attendance record
            $attendance = Attendance::updateOrCreate(
                [
                    'event_id' => $event->id,
                    'user_id' => $user->id,
                    'application_id' => $application->id,
                ],
                [
                    'checked_in_at' => now(),
                    'checkin_latitude' => $request->latitude,
                    'checkin_longitude' => $request->longitude,
                    'checkin_qr_code' => $request->qr_data,
                    'checkin_device_info' => $request->device_info,
                    'checkin_notes' => $request->notes,
                    'distance_from_event' => $distanceFromEvent,
                    'is_valid_checkin' => $isValidLocation,
                    'status' => 'checked_in',
                    'metadata' => [
                        'checkin_ip' => $request->ip(),
                        'checkin_user_agent' => $request->userAgent(),
                        'checkin_timestamp' => now()->timestamp,
                    ],
                ]
            );

            return response()->json([
                'success' => true,
                'message' => 'Successfully checked in',
                'attendance' => $attendance,
                'distance_warning' => !$isValidLocation ? 'You are far from the event location' : null,
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to process check-in: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Process QR code check-out.
     */
    public function checkout(Request $request)
    {
        $request->validate([
            'qr_data' => 'required|string',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'device_info' => 'nullable|string|max:500',
            'notes' => 'nullable|string|max:1000',
        ]);

        try {
            $qrData = json_decode(base64_decode($request->qr_data), true);
            
            if (!$qrData || !isset($qrData['event_id'], $qrData['user_id'])) {
                return response()->json(['error' => 'Invalid QR code data'], 400);
            }

            $event = Event::findOrFail($qrData['event_id']);
            $user = User::findOrFail($qrData['user_id']);

            $attendance = Attendance::where('event_id', $event->id)
                                  ->where('user_id', $user->id)
                                  ->first();

            if (!$attendance || !$attendance->checked_in_at) {
                return response()->json(['error' => 'Must check in first'], 400);
            }

            if ($attendance->checked_out_at) {
                return response()->json(['error' => 'Already checked out'], 400);
            }

            // Calculate distance from event location if GPS provided
            $distanceFromEvent = null;
            $isValidLocation = true;

            if ($request->latitude && $request->longitude && $event->latitude && $event->longitude) {
                $distanceFromEvent = $this->calculateDistance(
                    $request->latitude, 
                    $request->longitude,
                    $event->latitude, 
                    $event->longitude
                );

                $isValidLocation = $distanceFromEvent <= 500;
            }

            // Update attendance record
            $attendance->update([
                'checked_out_at' => now(),
                'checkout_latitude' => $request->latitude,
                'checkout_longitude' => $request->longitude,
                'checkout_qr_code' => $request->qr_data,
                'checkout_device_info' => $request->device_info,
                'checkout_notes' => $request->notes,
                'is_valid_checkout' => $isValidLocation,
                'status' => 'checked_out',
                'actual_hours' => $attendance->calculateActualHours(),
                'metadata' => array_merge($attendance->metadata ?? [], [
                    'checkout_ip' => $request->ip(),
                    'checkout_user_agent' => $request->userAgent(),
                    'checkout_timestamp' => now()->timestamp,
                ]),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Successfully checked out',
                'attendance' => $attendance,
                'hours_worked' => $attendance->actual_hours,
                'distance_warning' => !$isValidLocation ? 'You are far from the event location' : null,
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to process check-out: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Verify attendance record by organizer.
     */
    public function verify(Request $request, Attendance $attendance)
    {
        $this->authorize('update', $attendance->event);

        $request->validate([
            'verified' => 'required|boolean',
            'notes' => 'nullable|string|max:1000',
        ]);

        $attendance->update([
            'verified_by_organizer' => $request->verified,
            'verified_by' => Auth::id(),
            'verified_at' => now(),
            'verification_notes' => $request->notes,
        ]);

        return response()->json([
            'success' => true,
            'message' => $request->verified ? 'Attendance verified' : 'Attendance rejected',
        ]);
    }

    /**
     * Export attendance data for an event.
     */
    public function export(Event $event)
    {
        $this->authorize('view', $event);

        $attendances = Attendance::with(['user', 'application'])
                                ->where('event_id', $event->id)
                                ->get();

        $csvData = [];
        $csvData[] = [
            'Volunteer Name',
            'Email',
            'Phone',
            'Check-in Time',
            'Check-out Time',
            'Hours Worked',
            'Status',
            'Verified',
            'Distance (meters)',
            'Notes'
        ];

        foreach ($attendances as $attendance) {
            $csvData[] = [
                $attendance->user->name,
                $attendance->user->email,
                $attendance->user->phone,
                $attendance->checked_in_at ? $attendance->checked_in_at->format('Y-m-d H:i:s') : '',
                $attendance->checked_out_at ? $attendance->checked_out_at->format('Y-m-d H:i:s') : '',
                $attendance->actual_hours ?? 0,
                $attendance->status_text,
                $attendance->verified_by_organizer ? 'Yes' : 'No',
                $attendance->distance_from_event ?? '',
                $attendance->verification_notes ?? '',
            ];
        }

        $filename = 'attendance-' . $event->slug . '-' . now()->format('Y-m-d') . '.csv';

        $callback = function() use ($csvData) {
            $file = fopen('php://output', 'w');
            foreach ($csvData as $row) {
                fputcsv($file, $row);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    /**
     * Calculate distance between two GPS coordinates using Haversine formula.
     */
    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371000; // Earth's radius in meters

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat/2) * sin($dLat/2) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon/2) * sin($dLon/2);
        $c = 2 * atan2(sqrt($a), sqrt(1-$a));

        return $earthRadius * $c; // Distance in meters
    }
}