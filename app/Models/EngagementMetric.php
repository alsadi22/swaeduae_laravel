<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EngagementMetric extends Model
{
    protected $fillable = [
        'user_id', 'date', 'events_viewed', 'events_applied', 'events_completed',
        'badges_earned', 'messages_sent', 'hours_volunteered', 'login_count',
        'daily_engagement_score',
    ];

    protected $casts = ['date' => 'date'];

    public function user(): BelongsTo 
    { 
        return $this->belongsTo(User::class); 
    }

    public function scopeByDate($q, $date) 
    { 
        return $q->where('date', $date); 
    }
}
