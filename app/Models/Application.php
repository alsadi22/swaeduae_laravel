<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Application extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'event_id',
        'status',
        'motivation',
        'skills',
        'availability',
        'experience',
        'custom_responses',
        'documents',
        'rejection_reason',
        'applied_at',
        'reviewed_at',
        'reviewed_by',
        'attended',
        'checked_in_at',
        'checked_out_at',
        'hours_completed',
        'rating',
        'feedback',
        'volunteer_feedback',
        'volunteer_rating',
    ];

    protected $casts = [
        'skills' => 'array',
        'availability' => 'array',
        'custom_responses' => 'array',
        'documents' => 'array',
        'applied_at' => 'datetime',
        'reviewed_at' => 'datetime',
        'checked_in_at' => 'datetime',
        'checked_out_at' => 'datetime',
        'attended' => 'boolean',
        'hours_completed' => 'decimal:2',
    ];

    /**
     * Get the user that owns the application.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the event that the application is for.
     */
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    /**
     * Get the user who reviewed the application.
     */
    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    /**
     * Scope a query to only include approved applications.
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope a query to only include pending applications.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Check if the application is approved.
     */
    public function getIsApprovedAttribute()
    {
        return $this->status === 'approved';
    }

    /**
     * Check if the volunteer attended the event.
     */
    public function getDidAttendAttribute()
    {
        return $this->attended && $this->checked_in_at && $this->checked_out_at;
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        // Send notification when status is updated
        static::updated(function ($application) {
            // Check if status was changed
            if ($application->isDirty('status')) {
                // Send notification to the user
                $application->user->sendApplicationStatusNotification(
                    $application, 
                    $application->status, 
                    $application->rejection_reason
                );
            }
        });
    }
}