<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Conversation extends Model
{
    protected $fillable = [
        'user_id_1',
        'user_id_2',
        'last_message',
        'last_message_at',
        'unread_count_1',
        'unread_count_2',
    ];

    protected $casts = [
        'last_message_at' => 'datetime',
    ];

    public function user1(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id_1');
    }

    public function user2(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id_2');
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id_1', $userId)->orWhere('user_id_2', $userId);
    }

    public function getOtherUser($currentUserId)
    {
        return $this->user_id_1 === $currentUserId ? $this->user2 : $this->user1;
    }

    public function getUnreadCount($userId)
    {
        return $this->user_id_1 === $userId ? $this->unread_count_1 : $this->unread_count_2;
    }
}
