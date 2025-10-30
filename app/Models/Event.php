<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'requirements',
        'organization_id',
        'category_id',
        'category',
        'tags',
        'start_date',
        'end_date',
        'start_time',
        'end_time',
        'location',
        'address',
        'city',
        'emirate',
        'latitude',
        'longitude',
        'max_volunteers',
        'min_age',
        'max_age',
        'skills_required',
        'volunteer_hours',
        'image',
        'gallery',
        'status',
        'rejection_reason',
        'approved_at',
        'approved_by',
        'is_featured',
        'requires_application',
        'application_deadline',
        'contact_person',
        'contact_email',
        'contact_phone',
        'custom_fields',
    ];

    protected $casts = [
        'tags' => 'array',
        'skills_required' => 'array',
        'gallery' => 'array',
        'custom_fields' => 'array',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'application_deadline' => 'datetime',
        'approved_at' => 'datetime',
        'is_featured' => 'boolean',
        'requires_application' => 'boolean',
        'volunteer_hours' => 'decimal:2',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
    ];

    /**
     * Get the organization that owns the event.
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    /**
     * Get the category that the event belongs to.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the applications for the event.
     */
    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }

    /**
     * Get the approved applications for the event.
     */
    public function approvedApplications(): HasMany
    {
        return $this->applications()->where('status', 'approved');
    }

    /**
     * Get the volunteers for the event.
     */
    public function volunteers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'applications')
                    ->wherePivot('status', 'approved')
                    ->withPivot(['status', 'applied_at', 'attended', 'hours_completed']);
    }

    /**
     * Get the attendance records for the event.
     */
    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    /**
     * Get the certificates issued for this event.
     */
    public function certificates(): HasMany
    {
        return $this->hasMany(Certificate::class);
    }

    /**
     * Get the user who approved the event.
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Scope a query to only include published events.
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    /**
     * Scope a query to only include upcoming events.
     */
    public function scopeUpcoming($query)
    {
        return $query->where('start_date', '>', now());
    }

    /**
     * Scope a query to only include featured events.
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Get available spots for the event.
     */
    public function getAvailableSpotsAttribute()
    {
        return $this->max_volunteers - $this->approvedApplications()->count();
    }

    /**
     * Check if the event is full.
     */
    public function getIsFullAttribute()
    {
        return $this->available_spots <= 0;
    }

    /**
     * Check if applications are open.
     */
    public function getApplicationsOpenAttribute()
    {
        return $this->requires_application && 
               $this->application_deadline > now() && 
               !$this->is_full;
    }
}
