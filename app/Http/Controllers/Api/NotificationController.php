<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\NotificationService;
use App\Models\NotificationPreference;
use App\Models\DeviceToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Get notification preferences
     */
    public function preferences()
    {
        $preferences = NotificationPreference::where('user_id', Auth::id())
            ->first() ?? new NotificationPreference(['user_id' => Auth::id()]);

        return response()->json($preferences);
    }

    /**
     * Update preferences
     */
    public function updatePreferences(Request $request)
    {
        $validated = $request->validate([
            'email_notifications' => 'boolean',
            'sms_notifications' => 'boolean',
            'whatsapp_notifications' => 'boolean',
            'push_notifications' => 'boolean',
            'event_notifications' => 'boolean',
            'marketing_notifications' => 'boolean',
            'reminder_notifications' => 'boolean',
            'digest_notifications' => 'boolean',
            'digest_frequency' => 'in:daily,weekly,monthly',
            'quiet_hours_start' => 'nullable|date_format:H:i',
            'quiet_hours_end' => 'nullable|date_format:H:i',
        ]);

        $prefs = NotificationPreference::updateOrCreate(
            ['user_id' => Auth::id()],
            $validated
        );

        return response()->json($prefs);
    }

    /**
     * Register device
     */
    public function registerDevice(Request $request)
    {
        $validated = $request->validate([
            'token' => 'required|string|unique:device_tokens',
            'device_type' => 'required|in:ios,android,web',
            'device_name' => 'nullable|string',
            'app_version' => 'nullable|string',
            'os_version' => 'nullable|string',
        ]);

        $device = DeviceToken::create([
            'user_id' => Auth::id(),
            ...$validated,
            'is_active' => true,
            'last_used_at' => now(),
        ]);

        return response()->json($device, 201);
    }

    /**
     * Unregister device
     */
    public function unregisterDevice($token)
    {
        DeviceToken::where('user_id', Auth::id())
            ->where('token', $token)
            ->update(['is_active' => false]);

        return response()->json(['success' => true]);
    }

    /**
     * Get devices
     */
    public function devices()
    {
        $devices = DeviceToken::where('user_id', Auth::id())
            ->where('is_active', true)
            ->get();

        return response()->json($devices);
    }

    /**
     * Test notification
     */
    public function testNotification(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:sms,whatsapp,email,push',
        ]);

        $user = Auth::user();

        if ($validated['type'] === 'sms') {
            $result = $this->notificationService->sendSms(
                $user->id,
                $user->phone,
                'Test SMS from SwaedUAE'
            );
        } elseif ($validated['type'] === 'whatsapp') {
            $result = $this->notificationService->sendWhatsapp(
                $user->id,
                $user->phone,
                'Test WhatsApp message from SwaedUAE'
            );
        } elseif ($validated['type'] === 'email') {
            $result = $this->notificationService->sendEmail(
                $user->id,
                $user->email,
                'Test Email from SwaedUAE',
                'This is a test notification.'
            );
        } elseif ($validated['type'] === 'push') {
            $result = $this->notificationService->sendPushNotification(
                $user->id,
                'Test Notification',
                'This is a test push notification from SwaedUAE'
            );
        }

        if ($result['success']) {
            return response()->json(['message' => 'Test notification sent']);
        }

        return response()->json(['error' => $result['error']], 400);
    }
}
