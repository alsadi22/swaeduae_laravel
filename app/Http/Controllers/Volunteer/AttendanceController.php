<?php

namespace App\Http\Controllers\Volunteer;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Event;
use App\Models\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    /**
     * Display the QR code scanner interface
     */
    public function scanner()
    {
        $user = Auth::user();
        
        // Get recent attendance records
        $recentAttendance = Attendance::with(['event', 'application'])
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        return view('volunteer.attendance.scanner', compact('recentAttendance'));
    }
    
    /**
     * Process QR code scan for check-in/out
     */
    public function scan(Request $request)
    {
        $request->validate([
            'qr_data' => 'required|string',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'device_info' => 'required|array'
        ]);
        
        try {
            DB::beginTransaction();
            
            // Decode QR data (format: event_id:application_id:timestamp:hash)
            $qrParts = explode(':', $request->qr_data);
            if (count($qrParts) !== 4) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid QR code format'
                ], 400);
            }
            
            [$eventId, $applicationId, $timestamp, $hash] = $qrParts;
            
            // Verify QR code authenticity
            $expectedHash = hash('sha256', $eventId . ':' . $applicationId . ':' . $timestamp . ':' . config('app.key'));
            if (!hash_equals($expectedHash, $hash)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid or tampered QR code'
                ], 400);
            }
            
            // Check if QR code is expired (valid for 24 hours)
            $qrTimestamp = Carbon::createFromTimestamp($timestamp);
            if ($qrTimestamp->diffInHours(now()) > 24) {
                return response()->json([
                    'success' => false,
                    'message' => 'QR code has expired'
                ], 400);
            }
            
            // Get event and application
            $event = Event::findOrFail($eventId);
            $application = Application::where('id', $applicationId)
                ->where('user_id', Auth::id())
                ->where('status', 'approved')
                ->firstOrFail();
            
            // Check if event is active
            if ($event->status !== 'published') {
                return response()->json([
                    'success' => false,
                    'message' => 'Event is not currently active'
                ], 400);
            }
            
            // Calculate distance from event location
            $distance = $this->calculateDistance(
                $request->latitude,
                $request->longitude,
                $event->latitude,
                $event->longitude
            );
            
            // Check if within acceptable range (500 meters)
            $maxDistance = 0.5; // km
            $isLocationValid = $distance <= $maxDistance;
            
            // Get or create attendance record
            $attendance = Attendance::firstOrCreate([
                'user_id' => Auth::id(),
                'event_id' => $eventId,
                'application_id' => $applicationId
            ], [
                'status' => 'pending',
                'qr_code_data' => $request->qr_data,
                'device_info' => json_encode($request->device_info),
                'created_at' => now()
            ]);
            
            $action = null;
            $message = '';
            
            // Determine action based on current status
            if (!$attendance->checked_in_at) {
                // Check-in
                $attendance->update([
                    'checked_in_at' => now(),
                    'checkin_latitude' => $request->latitude,
                    'checkin_longitude' => $request->longitude,
                    'checkin_distance_km' => $distance,
                    'checkin_location_valid' => $isLocationValid,
                    'status' => 'checked_in'
                ]);
                
                $action = 'checkin';
                $message = $isLocationValid 
                    ? 'Successfully checked in to the event!'
                    : 'Checked in, but location verification failed. Please contact the organizer.';
                    
            } elseif (!$attendance->checked_out_at) {
                // Check-out
                $checkinTime = Carbon::parse($attendance->checked_in_at);
                $checkoutTime = now();
                $hoursWorked = $checkinTime->diffInMinutes($checkoutTime) / 60;
                
                $attendance->update([
                    'checked_out_at' => $checkoutTime,
                    'checkout_latitude' => $request->latitude,
                    'checkout_longitude' => $request->longitude,
                    'checkout_distance_km' => $distance,
                    'checkout_location_valid' => $isLocationValid,
                    'hours_worked' => round($hoursWorked, 2),
                    'status' => 'completed'
                ]);
                
                $action = 'checkout';
                $message = $isLocationValid 
                    ? "Successfully checked out! You worked {$attendance->hours_worked} hours."
                    : 'Checked out, but location verification failed. Please contact the organizer.';
                    
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'You have already completed attendance for this event'
                ], 400);
            }
            
            // Log the activity
            Log::info('Volunteer attendance processed', [
                'user_id' => Auth::id(),
                'event_id' => $eventId,
                'action' => $action,
                'location_valid' => $isLocationValid,
                'distance' => $distance
            ]);
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => [
                    'type' => $action,
                    'event' => [
                        'id' => $event->id,
                        'title' => $event->title
                    ],
                    'attendance' => [
                        'id' => $attendance->id,
                        'status' => $attendance->status,
                        'hours_worked' => $attendance->hours_worked,
                        'location_valid' => $isLocationValid
                    ]
                ]
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('QR scan processing failed', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'qr_data' => $request->qr_data
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to process attendance. Please try again or contact support.'
            ], 500);
        }
    }
    
    /**
     * Display volunteer's attendance history
     */
    public function history(Request $request)
    {
        $user = Auth::user();
        
        $query = Attendance::with(['event', 'application'])
            ->where('user_id', $user->id);
        
        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        // Filter by date range
        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }
        
        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }
        
        // Search by event title
        if ($request->filled('search')) {
            $query->whereHas('event', function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%');
            });
        }
        
        $attendances = $query->orderBy('created_at', 'desc')
            ->paginate(15)
            ->withQueryString();
        
        // Calculate statistics
        $stats = [
            'total_events' => $attendances->total(),
            'completed_events' => Attendance::where('user_id', $user->id)
                ->where('status', 'completed')
                ->count(),
            'total_hours' => Attendance::where('user_id', $user->id)
                ->where('status', 'completed')
                ->sum('hours_worked'),
            'pending_verification' => Attendance::where('user_id', $user->id)
                ->where('status', 'completed')
                ->where('organizer_verified', false)
                ->count()
        ];
        
        return view('volunteer.attendance.history', compact('attendances', 'stats'));
    }
    
    /**
     * Show specific attendance details
     */
    public function show(Attendance $attendance)
    {
        // Ensure user can only view their own attendance
        if ($attendance->user_id !== Auth::id()) {
            abort(403);
        }
        
        $attendance->load(['event', 'application']);
        
        return view('volunteer.attendance.show', compact('attendance'));
    }
    
    /**
     * Calculate distance between two coordinates using Haversine formula
     */
    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        if (is_null($lat2) || is_null($lon2)) {
            return null;
        }
        
        $earthRadius = 6371; // Earth's radius in kilometers
        
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        
        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon / 2) * sin($dLon / 2);
        
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        
        return $earthRadius * $c;
    }
}