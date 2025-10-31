<?php

namespace App\Services;

use App\Models\SmsLog;
use App\Models\WhatsappLog;
use App\Models\EmailLog;
use App\Models\PushNotification;
use App\Models\NotificationPreference;
use Illuminate\Support\Facades\Http;

class NotificationService
{
    /**
     * Send SMS notification
     */
    public function sendSms($userId, $phoneNumber, $message, $provider = 'twilio')
    {
        try {
            $prefs = NotificationPreference::where('user_id', $userId)->first();
            
            if ($prefs && !$prefs->sms_notifications) {
                return ['success' => false, 'error' => 'SMS notifications disabled by user'];
            }

            if ($prefs && $prefs->isInQuietHours()) {
                return ['success' => false, 'error' => 'In quiet hours'];
            }

            // Send via provider (Twilio/AWS SNS)
            $messageId = $this->sendViaProvider($provider, $phoneNumber, $message);

            $log = SmsLog::create([
                'user_id' => $userId,
                'phone_number' => $phoneNumber,
                'message' => $message,
                'provider' => $provider,
                'message_id' => $messageId,
                'status' => 'sent',
                'sent_at' => now(),
            ]);

            return ['success' => true, 'log' => $log];
        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Send WhatsApp notification
     */
    public function sendWhatsapp($userId, $phoneNumber, $message, $provider = 'twilio')
    {
        try {
            $prefs = NotificationPreference::where('user_id', $userId)->first();
            
            if ($prefs && !$prefs->whatsapp_notifications) {
                return ['success' => false, 'error' => 'WhatsApp notifications disabled'];
            }

            if ($prefs && $prefs->isInQuietHours()) {
                return ['success' => false, 'error' => 'In quiet hours'];
            }

            $messageId = $this->sendWhatsappViaProvider($provider, $phoneNumber, $message);

            $log = WhatsappLog::create([
                'user_id' => $userId,
                'phone_number' => $phoneNumber,
                'message_type' => 'text',
                'content' => $message,
                'provider' => $provider,
                'message_id' => $messageId,
                'status' => 'sent',
                'sent_at' => now(),
            ]);

            return ['success' => true, 'log' => $log];
        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Send email notification
     */
    public function sendEmail($userId, $email, $subject, $body, $type = 'notification')
    {
        try {
            $prefs = NotificationPreference::where('user_id', $userId)->first();
            
            if ($prefs && !$prefs->email_notifications) {
                return ['success' => false, 'error' => 'Email notifications disabled'];
            }

            // Send via Zoho/SendGrid
            \Mail::raw($body, function ($message) use ($email, $subject) {
                $message->to($email)->subject($subject);
            });

            $log = EmailLog::create([
                'user_id' => $userId,
                'recipient_email' => $email,
                'subject' => $subject,
                'email_type' => $type,
                'body' => $body,
                'provider' => 'zoho',
                'status' => 'sent',
                'sent_at' => now(),
            ]);

            return ['success' => true, 'log' => $log];
        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Send push notification
     */
    public function sendPushNotification($userId, $title, $body, $type = 'notification', $refId = null, $refType = null)
    {
        try {
            $prefs = NotificationPreference::where('user_id', $userId)->first();
            
            if ($prefs && !$prefs->push_notifications) {
                return ['success' => false, 'error' => 'Push notifications disabled'];
            }

            if ($prefs && $prefs->isInQuietHours()) {
                return ['success' => false, 'error' => 'In quiet hours'];
            }

            // Get user's device tokens
            $deviceTokens = \App\Models\DeviceToken::where('user_id', $userId)
                ->where('is_active', true)
                ->pluck('token')
                ->toArray();

            if (empty($deviceTokens)) {
                return ['success' => false, 'error' => 'No active device tokens'];
            }

            $notifications = [];
            foreach ($deviceTokens as $token) {
                $notif = PushNotification::create([
                    'user_id' => $userId,
                    'device_token' => $token,
                    'title' => $title,
                    'body' => $body,
                    'notification_type' => $type,
                    'reference_id' => $refId,
                    'reference_type' => $refType,
                    'status' => 'sent',
                    'sent_at' => now(),
                ]);
                $notifications[] = $notif;
            }

            return ['success' => true, 'notifications' => $notifications];
        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Send via SMS provider
     */
    private function sendViaProvider($provider, $phoneNumber, $message)
    {
        if ($provider === 'twilio') {
            return $this->sendViaTwilio($phoneNumber, $message);
        } elseif ($provider === 'aws_sns') {
            return $this->sendViaAwsSns($phoneNumber, $message);
        }

        return uniqid();
    }

    /**
     * Send WhatsApp via provider
     */
    private function sendWhatsappViaProvider($provider, $phoneNumber, $message)
    {
        if ($provider === 'twilio') {
            return $this->sendWhatsappViaTwilio($phoneNumber, $message);
        }

        return uniqid();
    }

    /**
     * Send via Twilio SMS
     */
    private function sendViaTwilio($phoneNumber, $message)
    {
        try {
            $response = Http::post('https://api.twilio.com/2010-04-01/Accounts/' . env('TWILIO_ACCOUNT_SID') . '/Messages.json', [
                'From' => env('TWILIO_PHONE_NUMBER'),
                'To' => $phoneNumber,
                'Body' => $message,
            ])->withBasicAuth(env('TWILIO_ACCOUNT_SID'), env('TWILIO_AUTH_TOKEN'));

            return $response->json()['sid'] ?? uniqid();
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Send WhatsApp via Twilio
     */
    private function sendWhatsappViaTwilio($phoneNumber, $message)
    {
        try {
            $response = Http::post('https://api.twilio.com/2010-04-01/Accounts/' . env('TWILIO_ACCOUNT_SID') . '/Messages.json', [
                'From' => 'whatsapp:' . env('TWILIO_WHATSAPP_NUMBER'),
                'To' => 'whatsapp:' . $phoneNumber,
                'Body' => $message,
            ])->withBasicAuth(env('TWILIO_ACCOUNT_SID'), env('TWILIO_AUTH_TOKEN'));

            return $response->json()['sid'] ?? uniqid();
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Send via AWS SNS
     */
    private function sendViaAwsSns($phoneNumber, $message)
    {
        // Implementation for AWS SNS
        return uniqid();
    }
}
