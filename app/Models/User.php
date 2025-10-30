<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'unique_id',
        'name',
        'email',
        'password',
        'phone',
        'date_of_birth',
        'gender',
        'nationality',
        'emirates_id',
        'address',
        'city',
        'emirate',
        'postal_code',
        'emergency_contact_name',
        'emergency_contact_phone',
        'emergency_contact_relationship',
        'skills',
        'interests',
        'bio',
        'avatar',
        'languages',
        'education_level',
        'occupation',
        'has_transportation',
        'availability',
        'total_volunteer_hours',
        'total_events_attended',
        'points',
        'is_verified',
        'verified_at',
        'profile_completed',
        'notification_preferences',
        'privacy_settings',
        'last_active_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'date_of_birth' => 'date',
        'skills' => 'array',
        'interests' => 'array',
        'languages' => 'array',
        'availability' => 'array',
        'notification_preferences' => 'array',
        'privacy_settings' => 'array',
        'has_transportation' => 'boolean',
        'is_verified' => 'boolean',
        'profile_completed' => 'boolean',
        'verified_at' => 'datetime',
        'last_active_at' => 'datetime',
        'total_volunteer_hours' => 'decimal:2',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        // Auto-generate unique ID when creating a new user
        static::creating(function ($user) {
            if (empty($user->unique_id)) {
                // Get the next auto-increment ID
                $nextId = \Illuminate\Support\Facades\DB::table('users')->max('id') + 1;
                $user->unique_id = 'SV' . str_pad($nextId, 6, '0', STR_PAD_LEFT);
            }
        });
    }

    /**
     * Get the applications for the user.
     */
    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }

    /**
     * Get the certificates for the user.
     */
    public function certificates(): HasMany
    {
        return $this->hasMany(Certificate::class);
    }

    /**
     * Get the attendance records for the user.
     */
    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    /**
     * Get the badges earned by the user.
     */
    public function badges(): BelongsToMany
    {
        return $this->belongsToMany(Badge::class, 'user_badges')
                    ->withPivot(['earned_at', 'metadata'])
                    ->withTimestamps();
    }

    /**
     * Get the organizations the user belongs to.
     */
    public function organizations(): BelongsToMany
    {
        return $this->belongsToMany(Organization::class, 'organization_users')
                    ->withPivot(['role', 'joined_at', 'is_active'])
                    ->withTimestamps();
    }

    /**
     * Get the events the user has applied to.
     */
    public function events(): BelongsToMany
    {
        return $this->belongsToMany(Event::class, 'applications')
                    ->withPivot(['status', 'applied_at', 'attended', 'hours_completed']);
    }

    /**
     * Get the approved applications for the user.
     */
    public function approvedApplications(): HasMany
    {
        return $this->applications()->where('status', 'approved');
    }

    /**
     * Get the user's age.
     */
    public function getAgeAttribute()
    {
        return $this->date_of_birth ? $this->date_of_birth->age : null;
    }

    /**
     * Check if the user's profile is complete.
     */
    public function getIsProfileCompleteAttribute()
    {
        $requiredFields = [
            'name', 'email', 'phone', 'date_of_birth', 'gender',
            'nationality', 'city', 'emirate', 'emergency_contact_name',
            'emergency_contact_phone'
        ];

        foreach ($requiredFields as $field) {
            if (empty($this->$field)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Update the user's last active timestamp.
     */
    public function updateLastActive()
    {
        $this->update(['last_active_at' => now()]);
    }

    /**
     * Award points to the user.
     */
    public function awardPoints(int $points, string $reason = null)
    {
        $this->increment('points', $points);
        
        // You can log the points award here if needed
        // PointsHistory::create([
        //     'user_id' => $this->id,
        //     'points' => $points,
        //     'reason' => $reason,
        // ]);
    }

    /**
     * Send application status notification.
     *
     * @param  \App\Models\Application  $application
     * @param  string  $status
     * @param  string|null  $reason
     * @return void
     */
    public function sendApplicationStatusNotification($application, $status, $reason = null)
    {
        $this->notify(new \App\Notifications\ApplicationStatusUpdated($application, $status, $reason));
    }

    /**
     * Send attendance notification.
     *
     * @param  \App\Models\Attendance  $attendance
     * @param  string  $type
     * @return void
     */
    public function sendAttendanceNotification($attendance, $type)
    {
        $this->notify(new \App\Notifications\AttendanceUpdated($attendance, $type));
    }

    /**
     * Send certificate issued notification.
     *
     * @param  \App\Models\Certificate  $certificate
     * @return void
     */
    public function sendCertificateNotification($certificate)
    {
        $this->notify(new \App\Notifications\CertificateIssued($certificate));
    }

    /**
     * Send badge earned notification.
     *
     * @param  \App\Models\Badge  $badge
     * @param  int  $points
     * @return void
     */
    public function sendBadgeNotification($badge, $points)
    {
        $this->notify(new \App\Notifications\BadgeEarned($badge, $points));
    }

    /**
     * Send event update notification.
     *
     * @param  \App\Models\Event  $event
     * @param  string  $updateType
     * @param  string|null  $message
     * @return void
     */
    public function sendEventNotification($event, $updateType, $message = null)
    {
        $this->notify(new \App\Notifications\EventUpdate($event, $updateType, $message));
    }
}