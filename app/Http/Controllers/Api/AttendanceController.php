<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Event;
use App\Models\Application;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    /**
     * Process QR code scan for check-in/out
     */
    public function scan(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'qr_data' => 'required|string',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'device_info' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid input data',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Decode QR data
            $qrData = json_decode($request->qr_data, true);
            
            // If JSON decode fails, try to decode as base64 encoded JSON
            if (!$qrData) {
                $decoded = base64_decode($request->qr_data);
                $qrData = json_decode($decoded, true);
            }
            
            if (!$qrData || !isset($qrData['event_id'], $qrData['user_id'], $qrData['type'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid QR code format'
                ], 400);
            }

            // Verify user matches QR code
            if ($qrData['user_id'] != Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'QR code does not belong to you'
                ], 403);
            }

            // Find event and application
            $event = Event::findOrFail($qrData['event_id']);
            $application = Application::where('event_id', $event->id)
                ->where('user_id', Auth::id())
                ->where('status', 'approved')
                ->first();

            if (!$application) {
                return response()->json([
                    'success' => false,
                    'message' => 'No approved application found for this event'
                ], 404);
            }

            // Check if event is today or within a reasonable time window
            $today = Carbon::today();
            $eventDate = Carbon::parse($event->start_date)->startOfDay();
            
            if (!$eventDate->isSameDay($today)) {
                // Allow check-in up to 1 day before and 1 day after event date
                $allowedWindowStart = $eventDate->copy()->subDay();
                $allowedWindowEnd = $eventDate->copy()->addDay();
                
                if (!$today->between($allowedWindowStart, $allowedWindowEnd)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Event is not scheduled for today. Check-in is only allowed on the event day.'
                    ], 400);
                }
            }

            // Find or create attendance record
            $attendance = Attendance::firstOrCreate([
                'user_id' => Auth::id(),
                'event_id' => $event->id,
                'application_id' => $application->id
            ]);

            // Validate location if provided
            $locationValidated = true;
            $distanceFromEvent = null;
            
            if ($request->latitude && $request->longitude && $event->latitude && $event->longitude) {
                $distanceFromEvent = $this->calculateDistance(
                    $request->latitude, 
                    $request->longitude,
                    $event->latitude, 
                    $event->longitude
                );
                $locationValidated = $distanceFromEvent <= 100; // 100 meters tolerance
            }

            // Process check-in or check-out
            if ($qrData['type'] === 'checkin') {
                if ($attendance->checked_in_at) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Already checked in',
                        'data' => [
                            'attendance' => $attendance,
                            'checked_in_at' => $attendance->checked_in_at->toISOString()
                        ]
                    ], 400);
                }

                $attendance->update([
                    'checked_in_at' => now(),
                    'latitude' => $request->latitude,
                    'longitude' => $request->longitude,
                    'qr_code_data' => $request->qr_data,
                    'device_info' => $request->device_info,
                    'location_validated' => $locationValidated,
                    'distance_from_event' => $distanceFromEvent,
                    'status' => 'checked_in'
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Successfully checked in',
                    'data' => [
                        'attendance' => $attendance->fresh(),
                        'event' => $event,
                        'location_validated' => $locationValidated,
                        'distance' => $distanceFromEvent
                    ]
                ]);

            } elseif ($qrData['type'] === 'checkout') {
                if (!$attendance->checked_in_at) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Must check in first'
                    ], 400);
                }

                if ($attendance->checked_out_at) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Already checked out',
                        'data' => [
                            'attendance' => $attendance,
                            'checked_out_at' => $attendance->checked_out_at->toISOString()
                        ]
                    ], 400);
                }

                $hoursWorked = $attendance->checked_in_at->diffInHours(now(), true);

                $attendance->update([
                    'checked_out_at' => now(),
                    'hours_worked' => round($hoursWorked, 2),
                    'status' => 'completed'
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Successfully checked out',
                    'data' => [
                        'attendance' => $attendance->fresh(),
                        'hours_worked' => $attendance->hours_worked
                    ]
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Invalid QR code type'
            ], 400);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error processing QR code: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get user's attendance history
     */
    public function history(Request $request): JsonResponse
    {
        $query = Attendance::with(['event', 'event.organization'])
            ->where('user_id', Auth::id());

        // Apply filters
        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->from_date) {
            $query->whereHas('event', function ($q) use ($request) {
                $q->where('start_date', '>=', $request->from_date);
            });
        }

        if ($request->to_date) {
            $query->whereHas('event', function ($q) use ($request) {
                $q->where('start_date', '<=', $request->to_date);
            });
        }

        if ($request->search) {
            $query->whereHas('event', function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%');
            });
        }

        $attendances = $query->orderBy('created_at', 'desc')
            ->paginate($request->per_page ?? 15);

        // Calculate statistics
        $stats = [
            'total_events' => Attendance::where('user_id', Auth::id())->count(),
            'completed_events' => Attendance::where('user_id', Auth::id())
                ->where('status', 'completed')->count(),
            'total_hours' => Attendance::where('user_id', Auth::id())
                ->sum('hours_worked') ?? 0,
            'pending_verification' => Attendance::where('user_id', Auth::id())
                ->where('status', 'completed')
                ->where('organizer_verified', false)->count()
        ];

        return response()->json([
            'success' => true,
            'data' => [
                'attendances' => $attendances,
                'stats' => $stats
            ]
        ]);
    }

    /**
     * Get specific attendance details
     */
    public function show(Attendance $attendance): JsonResponse
    {
        // Check if user owns this attendance record
        if ($attendance->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $attendance->load(['event', 'event.organization', 'application']);

        return response()->json([
            'success' => true,
            'data' => [
                'attendance' => $attendance
            ]
        ]);
    }

    /**
     * Validate location for attendance
     */
    public function validateLocation(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'event_id' => 'required|exists:events,id',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid input data',
                'errors' => $validator->errors()
            ], 422);
        }

        $event = Event::findOrFail($request->event_id);

        if (!$event->latitude || !$event->longitude) {
            return response()->json([
                'success' => false,
                'message' => 'Event location not available'
            ], 400);
        }

        $distance = $this->calculateDistance(
            $request->latitude,
            $request->longitude,
            $event->latitude,
            $event->longitude
        );

        $isValid = $distance <= 100; // 100 meters tolerance

        return response()->json([
            'success' => true,
            'data' => [
                'distance' => round($distance, 2),
                'is_valid' => $isValid,
                'max_distance' => 100
            ]
        ]);
    }

    /**
     * Get event attendance records (for organizations)
     */
    public function eventAttendance(Event $event): JsonResponse
    {
        // Check if user is organization member
        if (!Auth::user()->organizations->contains($event->organization_id)) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $attendances = Attendance::with(['user', 'application'])
            ->where('event_id', $event->id)
            ->orderBy('created_at', 'desc')
            ->get();

        $stats = [
            'total_registered' => $event->applications()->where('status', 'approved')->count(),
            'checked_in' => $attendances->where('status', '!=', 'pending')->count(),
            'checked_out' => $attendances->where('status', 'completed')->count(),
            'verified' => $attendances->where('organizer_verified', true)->count()
        ];

        return response()->json([
            'success' => true,
            'data' => [
                'event' => $event,
                'attendances' => $attendances,
                'stats' => $stats
            ]
        ]);
    }

    /**
     * Verify attendance (for organizations)
     */
    public function verify(Request $request, Attendance $attendance): JsonResponse
    {
        // Check if user is organization member
        if (!Auth::user()->organizations->contains($attendance->event->organization_id)) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'verified' => 'required|boolean',
            'notes' => 'nullable|string|max:500'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid input data',
                'errors' => $validator->errors()
            ], 422);
        }

        $attendance->update([
            'organizer_verified' => $request->verified,
            'verified_at' => $request->verified ? now() : null,
            'verified_by' => $request->verified ? Auth::id() : null,
            'organizer_notes' => $request->notes
        ]);

        return response()->json([
            'success' => true,
            'message' => $request->verified ? 'Attendance verified' : 'Verification removed',
            'data' => [
                'attendance' => $attendance->fresh()
            ]
        ]);
    }

    /**
     * Calculate distance between two coordinates in meters
     */
    private function calculateDistance($lat1, $lon1, $lat2, $lon2): float
    {
        $earthRadius = 6371000; // Earth's radius in meters

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat/2) * sin($dLat/2) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon/2) * sin($dLon/2);
        $c = 2 * atan2(sqrt($a), sqrt(1-$a));

        return $earthRadius * $c;
    }
}