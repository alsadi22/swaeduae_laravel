<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Organization extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'email',
        'phone',
        'website',
        'address',
        'city',
        'emirate',
        'postal_code',
        'logo',
        'documents',
        'status',
        'rejection_reason',
        'approved_at',
        'approved_by',
        'is_verified',
        'social_media',
        'mission_statement',
        'founded_year',
        'organization_type',
        'focus_areas',
    ];

    protected $casts = [
        'documents' => 'array',
        'social_media' => 'array',
        'focus_areas' => 'array',
        'approved_at' => 'datetime',
        'is_verified' => 'boolean',
    ];

    /**
     * Get the events for the organization.
     */
    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }

    /**
     * Get the users associated with the organization.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'organization_users')
                    ->withPivot(['role', 'joined_at', 'is_active'])
                    ->withTimestamps();
    }

    /**
     * Get the certificates issued by this organization.
     */
    public function certificates(): HasMany
    {
        return $this->hasMany(Certificate::class);
    }

    /**
     * Scope a query to only include verified organizations.
     */
    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    /**
     * Scope a query to only include approved organizations.
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Get the organization's active events.
     */
    public function activeEvents(): HasMany
    {
        return $this->events()->whereIn('status', ['published', 'approved']);
    }
}
