<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Recommendation extends Model
{
    protected $fillable = [
        'user_id',
        'recommended_type',
        'recommended_id',
        'reason',
        'score',
        'metadata',
        'clicked',
        'clicked_at',
    ];

    protected $casts = [
        'metadata' => 'array',
        'clicked' => 'boolean',
        'clicked_at' => 'datetime',
        'score' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function recommended()
    {
        return $this->morphTo();
    }

    public function scopeTopScored($query, $limit = 10)
    {
        return $query->orderBy('score', 'desc')->limit($limit);
    }

    public function scopeUnclicked($query)
    {
        return $query->where('clicked', false);
    }
}
