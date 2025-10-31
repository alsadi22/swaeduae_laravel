<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SocialShare extends Model
{
    protected $fillable = [
        'user_id',
        'content_type',
        'content_id',
        'platform',
        'share_data',
        'shared_at',
    ];

    protected $casts = [
        'share_data' => 'array',
        'shared_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
