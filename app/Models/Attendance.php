<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'event_id',
        'application_id',
        'checked_in_at',
        'checkin_latitude',
        'checkin_longitude',
        'checkin_qr_code',
        'checkin_device_info',
        'checkin_notes',
        'checked_out_at',
        'checkout_latitude',
        'checkout_longitude',
        'checkout_qr_code',
        'checkout_device_info',
        'checkout_notes',
        'is_valid_checkin',
        'is_valid_checkout',
        'distance_from_event',
        'total_hours',
        'actual_hours',
        'status',
        'verified_by_organizer',
        'verified_by',
        'verified_at',
        'verification_notes',
        'metadata',
    ];

    protected $casts = [
        'checked_in_at' => 'datetime',
        'checked_out_at' => 'datetime',
        'verified_at' => 'datetime',
        'checkin_latitude' => 'decimal:8',
        'checkin_longitude' => 'decimal:8',
        'checkout_latitude' => 'decimal:8',
        'checkout_longitude' => 'decimal:8',
        'distance_from_event' => 'decimal:2',
        'actual_hours' => 'decimal:2',
        'is_valid_checkin' => 'boolean',
        'is_valid_checkout' => 'boolean',
        'verified_by_organizer' => 'boolean',
        'metadata' => 'array',
    ];

    /**
     * Get the user that this attendance record belongs to.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the event that this attendance record belongs to.
     */
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    /**
     * Get the application that this attendance record belongs to.
     */
    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class);
    }

    /**
     * Get the user who verified this attendance.
     */
    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    /**
     * Calculate the actual hours worked based on check-in and check-out times.
     */
    public function calculateActualHours(): ?float
    {
        if (!$this->checked_in_at || !$this->checked_out_at) {
            return null;
        }

        $checkinTime = Carbon::parse($this->checked_in_at);
        $checkoutTime = Carbon::parse($this->checked_out_at);

        return round($checkoutTime->diffInMinutes($checkinTime) / 60, 2);
    }

    /**
     * Check if the volunteer is currently checked in.
     */
    public function isCheckedIn(): bool
    {
        return $this->checked_in_at && !$this->checked_out_at;
    }

    /**
     * Check if the volunteer has completed their attendance (checked in and out).
     */
    public function isCompleted(): bool
    {
        return $this->checked_in_at && $this->checked_out_at;
    }

    /**
     * Check if the attendance is within the valid location range.
     */
    public function isWithinValidRange(float $maxDistance = 100): bool
    {
        return $this->distance_from_event <= $maxDistance;
    }

    /**
     * Get the status badge color for UI display.
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'checked_in' => 'blue',
            'checked_out' => 'green',
            'no_show' => 'red',
            'late' => 'yellow',
            'early_departure' => 'orange',
            default => 'gray'
        };
    }

    /**
     * Get the formatted status text.
     */
    public function getStatusTextAttribute(): string
    {
        return match($this->status) {
            'checked_in' => 'Checked In',
            'checked_out' => 'Checked Out',
            'no_show' => 'No Show',
            'late' => 'Late Arrival',
            'early_departure' => 'Early Departure',
            default => 'Unknown'
        };
    }

    /**
     * Scope to get attendance records for a specific event.
     */
    public function scopeForEvent($query, $eventId)
    {
        return $query->where('event_id', $eventId);
    }

    /**
     * Scope to get attendance records for a specific user.
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope to get only checked-in records.
     */
    public function scopeCheckedIn($query)
    {
        return $query->where('status', 'checked_in');
    }

    /**
     * Scope to get only completed attendance records.
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'checked_out');
    }

    /**
     * Scope to get verified attendance records.
     */
    public function scopeVerified($query)
    {
        return $query->where('verified_by_organizer', true);
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        // Send notification when attendance status is updated
        static::updated(function ($attendance) {
            // Check if status was changed
            if ($attendance->isDirty('status')) {
                $type = match($attendance->status) {
                    'checked_in' => 'checkin',
                    'checked_out' => 'checkout',
                    default => 'updated'
                };
                
                // Send notification to the user
                $attendance->user->sendAttendanceNotification($attendance, $type);
            }
            
            // Send notification when attendance is verified
            if ($attendance->isDirty('verified_by_organizer') && $attendance->verified_by_organizer) {
                // Send notification to the user
                $attendance->user->sendAttendanceNotification($attendance, 'verified');
            }
        });
        
        // Send notification when attendance is created (check-in)
        static::created(function ($attendance) {
            if ($attendance->checked_in_at) {
                // Send notification to the user
                $attendance->user->sendAttendanceNotification($attendance, 'checkin');
            }
        });
    }
}