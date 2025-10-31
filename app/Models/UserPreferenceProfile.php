<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserPreferenceProfile extends Model
{
    protected $fillable = [
        'user_id', 'preferred_event_types', 'preferred_skills', 'preferred_locations',
        'preferred_organizations', 'disliked_categories', 'average_engagement_score',
        'total_interactions', 'last_updated_at',
    ];

    protected $casts = [
        'preferred_event_types' => 'array',
        'preferred_skills' => 'array',
        'preferred_locations' => 'array',
        'preferred_organizations' => 'array',
        'disliked_categories' => 'array',
        'last_updated_at' => 'datetime',
    ];

    public function user(): BelongsTo 
    { 
        return $this->belongsTo(User::class); 
    }
}
