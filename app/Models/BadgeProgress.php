<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BadgeProgress extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'badge_id',
        'progress',
        'metadata',
    ];

    protected $casts = [
        'progress' => 'integer',
        'metadata' => 'array',
    ];

    /**
     * Get the user that owns this progress record.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the badge this progress record is for.
     */
    public function badge(): BelongsTo
    {
        return $this->belongsTo(Badge::class);
    }

    /**
     * Check if the progress meets the badge criteria.
     */
    public function isComplete(): bool
    {
        return $this->progress >= 100;
    }

    /**
     * Update progress and award badge if complete.
     */
    public function updateProgress(int $increment): bool
    {
        $this->increment('progress', $increment);
        
        // If progress is complete and badge hasn't been awarded yet
        if ($this->isComplete() && !$this->badge->isEarnedBy($this->user)) {
            return $this->badge->awardTo($this->user, $this->metadata ?? []);
        }
        
        return false;
    }
}