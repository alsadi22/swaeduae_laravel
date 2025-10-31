<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SearchPreference extends Model
{
    protected $fillable = [
        'user_id',
        'preferred_categories',
        'preferred_locations',
        'preferred_skills',
        'excluded_categories',
        'excluded_locations',
        'max_distance_km',
        'auto_recommendations',
        'recommendation_frequency',
    ];

    protected $casts = [
        'preferred_categories' => 'array',
        'preferred_locations' => 'array',
        'preferred_skills' => 'array',
        'excluded_categories' => 'array',
        'excluded_locations' => 'array',
        'auto_recommendations' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
