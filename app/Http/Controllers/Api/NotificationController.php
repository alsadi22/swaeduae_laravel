<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Attendance;
use App\Models\Certificate;
use App\Models\Badge;
use App\Models\Event;
use App\Notifications\ApplicationStatusUpdated;
use App\Notifications\AttendanceUpdated;
use App\Notifications\CertificateIssued;
use App\Notifications\BadgeEarned;
use App\Notifications\EventUpdate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Display a listing of the user's notifications.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 15);
        $unreadOnly = $request->get('unread_only', false);
        
        $query = Auth::user()->notifications();
        
        if ($unreadOnly) {
            $query->whereNull('read_at');
        }
        
        $notifications = $query->paginate($perPage);
        
        return response()->json($notifications);
    }

    /**
     * Mark a notification as read.
     *
     * @param  string  $id
     * @return \Illuminate\Http\Response
     */
    public function markAsRead($id)
    {
        $notification = Auth::user()->notifications()->where('id', $id)->first();
        
        if (!$notification) {
            return response()->json(['message' => 'Notification not found'], 404);
        }
        
        $notification->markAsRead();
        
        return response()->json(['message' => 'Notification marked as read']);
    }

    /**
     * Mark all notifications as read.
     *
     * @return \Illuminate\Http\Response
     */
    public function markAllAsRead()
    {
        Auth::user()->unreadNotifications()->update(['read_at' => now()]);
        
        return response()->json(['message' => 'All notifications marked as read']);
    }

    /**
     * Delete a notification.
     *
     * @param  string  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $notification = Auth::user()->notifications()->where('id', $id)->first();
        
        if (!$notification) {
            return response()->json(['message' => 'Notification not found'], 404);
        }
        
        $notification->delete();
        
        return response()->json(['message' => 'Notification deleted']);
    }

    /**
     * Get the count of unread notifications.
     *
     * @return \Illuminate\Http\Response
     */
    public function unreadCount()
    {
        $count = Auth::user()->unreadNotifications()->count();
        
        return response()->json(['count' => $count]);
    }

    /**
     * Update user's notification preferences.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updatePreferences(Request $request)
    {
        $validated = $request->validate([
            'email' => 'boolean',
            'push' => 'boolean',
            'sms' => 'boolean',
        ]);
        
        $preferences = Auth::user()->notification_preferences ?? [];
        
        foreach ($validated as $key => $value) {
            $preferences[$key] = $value;
        }
        
        Auth::user()->update(['notification_preferences' => $preferences]);
        
        return response()->json([
            'message' => 'Notification preferences updated',
            'preferences' => $preferences
        ]);
    }

    /**
     * Get user's notification preferences.
     *
     * @return \Illuminate\Http\Response
     */
    public function getPreferences()
    {
        $preferences = Auth::user()->notification_preferences ?? [
            'email' => true,
            'push' => true,
            'sms' => false,
        ];
        
        return response()->json($preferences);
    }

    /**
     * Send a test notification.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function sendTestNotification(Request $request)
    {
        $type = $request->get('type', 'application');
        
        switch ($type) {
            case 'application':
                // Create a fake application for testing
                $application = new Application([
                    'event_id' => 1,
                    'user_id' => Auth::id(),
                    'status' => 'approved'
                ]);
                
                // Create a fake event for the application
                $application->setRelation('event', new Event(['title' => 'Test Event']));
                
                Auth::user()->notify(new ApplicationStatusUpdated($application, 'approved'));
                break;
                
            case 'attendance':
                // Create a fake attendance for testing
                $attendance = new Attendance([
                    'event_id' => 1,
                    'user_id' => Auth::id(),
                ]);
                
                // Create a fake event for the attendance
                $attendance->setRelation('event', new Event(['title' => 'Test Event']));
                
                Auth::user()->notify(new AttendanceUpdated($attendance, 'checkin'));
                break;
                
            case 'certificate':
                // Create a fake certificate for testing
                $certificate = new Certificate([
                    'event_id' => 1,
                    'user_id' => Auth::id(),
                    'certificate_number' => 'CERT-TEST-001',
                    'hours_completed' => 5.0
                ]);
                
                // Create a fake event for the certificate
                $certificate->setRelation('event', new Event(['title' => 'Test Event']));
                
                Auth::user()->notify(new CertificateIssued($certificate));
                break;
                
            case 'badge':
                // Create a fake badge for testing
                $badge = new Badge([
                    'name' => 'Test Badge',
                    'description' => 'This is a test badge'
                ]);
                
                Auth::user()->notify(new BadgeEarned($badge, 100));
                break;
                
            case 'event':
                // Create a fake event for testing
                $event = new Event([
                    'title' => 'Test Event',
                ]);
                
                Auth::user()->notify(new EventUpdate($event, 'new', 'This is a test event notification'));
                break;
                
            default:
                return response()->json(['message' => 'Invalid notification type'], 400);
        }
        
        return response()->json(['message' => 'Test notification sent']);
    }
}