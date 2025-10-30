<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Badge extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'icon',
        'color',
        'type',
        'criteria',
        'points',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'criteria' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * Get the users who have earned this badge.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_badges')
                    ->withPivot(['earned_at', 'metadata'])
                    ->withTimestamps();
    }

    /**
     * Scope a query to only include active badges.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to order badges by sort order.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }

    /**
     * Check if a user has earned this badge.
     */
    public function isEarnedBy(User $user)
    {
        return $this->users()->where('user_id', $user->id)->exists();
    }

    /**
     * Award this badge to a user.
     */
    public function awardTo(User $user, array $metadata = [])
    {
        if (!$this->isEarnedBy($user)) {
            $this->users()->attach($user->id, [
                'earned_at' => now(),
                'metadata' => json_encode($metadata),
            ]);

            // Add points to user
            $user->increment('points', $this->points);
            
            // Send notification to user
            $user->sendBadgeNotification($this, $this->points);

            return true;
        }

        return false;
    }
}