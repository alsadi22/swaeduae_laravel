<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SavedSearch extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'search_type',
        'filters',
        'query',
        'notify_on_match',
        'notification_count',
        'last_notified_at',
        'last_used_at',
    ];

    protected $casts = [
        'filters' => 'array',
        'notify_on_match' => 'boolean',
        'last_notified_at' => 'datetime',
        'last_used_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeWithNotifications($query)
    {
        return $query->where('notify_on_match', true);
    }
}
