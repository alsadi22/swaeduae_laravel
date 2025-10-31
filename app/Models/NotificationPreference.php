<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotificationPreference extends Model
{
    protected $fillable = [
        'user_id',
        'email_notifications',
        'sms_notifications',
        'whatsapp_notifications',
        'push_notifications',
        'event_notifications',
        'marketing_notifications',
        'reminder_notifications',
        'digest_notifications',
        'digest_frequency',
        'notification_categories',
        'quiet_hours_start',
        'quiet_hours_end',
    ];

    protected $casts = [
        'email_notifications' => 'boolean',
        'sms_notifications' => 'boolean',
        'whatsapp_notifications' => 'boolean',
        'push_notifications' => 'boolean',
        'event_notifications' => 'boolean',
        'marketing_notifications' => 'boolean',
        'reminder_notifications' => 'boolean',
        'digest_notifications' => 'boolean',
        'notification_categories' => 'array',
        'quiet_hours_start' => 'time',
        'quiet_hours_end' => 'time',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isInQuietHours(): bool
    {
        if (!$this->quiet_hours_start || !$this->quiet_hours_end) {
            return false;
        }

        $now = now()->format('H:i');
        $start = $this->quiet_hours_start;
        $end = $this->quiet_hours_end;

        return $now >= $start && $now <= $end;
    }
}
