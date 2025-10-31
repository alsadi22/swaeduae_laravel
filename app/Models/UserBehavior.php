<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserBehavior extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'action_type', 'entity_type', 'entity_id', 'metadata',
        'engagement_score', 'device_type', 'duration_seconds', 'referrer',
    ];

    protected $casts = ['metadata' => 'array'];

    public function user(): BelongsTo 
    { 
        return $this->belongsTo(User::class); 
    }

    public function scopeByActionType($q, $type) 
    { 
        return $q->where('action_type', $type); 
    }

    public function scopeRecent($q) 
    { 
        return $q->orderBy('created_at', 'desc'); 
    }
}
