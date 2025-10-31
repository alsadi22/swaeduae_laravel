<?php

namespace App\Http\Controllers\Volunteer;

use App\Http\Controllers\Controller;
use App\Services\NotificationService;
use App\Models\NotificationPreference;
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
     * Show notification settings
     */
    public function settings()
    {
        $preferences = NotificationPreference::where('user_id', Auth::id())
            ->first() ?? new NotificationPreference();

        return view('volunteer.notifications.settings', compact('preferences'));
    }

    /**
     * Update notification preferences
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

        NotificationPreference::updateOrCreate(
            ['user_id' => Auth::id()],
            $validated
        );

        return back()->with('success', 'Notification preferences updated');
    }

    /**
     * Register device token
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

        \App\Models\DeviceToken::create([
            'user_id' => Auth::id(),
            ...$validated,
            'is_active' => true,
            'last_used_at' => now(),
        ]);

        return response()->json(['success' => true]);
    }

    /**
     * Unregister device token
     */
    public function unregisterDevice($token)
    {
        \App\Models\DeviceToken::where('user_id', Auth::id())
            ->where('token', $token)
            ->update(['is_active' => false]);

        return response()->json(['success' => true]);
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
                'This is a test SMS from SwaedUAE'
            );
        } elseif ($validated['type'] === 'email') {
            $result = $this->notificationService->sendEmail(
                $user->id,
                $user->email,
                'Test Email from SwaedUAE',
                'This is a test email to verify your notification settings.'
            );
        }

        if ($result['success']) {
            return back()->with('success', 'Test notification sent');
        }

        return back()->with('error', $result['error'] ?? 'Failed to send notification');
    }
}
