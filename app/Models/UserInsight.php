<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserInsight extends Model
{
    protected $fillable = [
        'user_id', 'engagement_level', 'activity_frequency', 'volunteer_type',
        'estimated_lifetime_value', 'behavior_patterns', 'risk_indicators',
        'opportunities', 'last_analyzed_at',
    ];

    protected $casts = [
        'behavior_patterns' => 'array', 'risk_indicators' => 'array',
        'opportunities' => 'array', 'last_analyzed_at' => 'datetime',
    ];

    public function user(): BelongsTo 
    { 
        return $this->belongsTo(User::class); 
    }
}
