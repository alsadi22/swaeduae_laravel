<?php

namespace App\Services;

use App\Models\Message;
use App\Models\Conversation;
use Illuminate\Support\Facades\DB;

class MessagingService
{
    /**
     * Send message
     */
    public function sendMessage($senderId, $recipientId, $content, $type = 'text', $mediaUrl = null)
    {
        try {
            $message = Message::create([
                'sender_id' => $senderId,
                'recipient_id' => $recipientId,
                'content' => $content,
                'message_type' => $type,
                'media_url' => $mediaUrl,
            ]);

            // Update or create conversation
            $this->updateConversation($senderId, $recipientId, $content);

            return ['success' => true, 'message' => $message];
        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Update conversation
     */
    private function updateConversation($senderId, $recipientId, $lastMessage)
    {
        $conversation = Conversation::where(function ($q) use ($senderId, $recipientId) {
            $q->where('user_id_1', $senderId)->where('user_id_2', $recipientId);
        })->orWhere(function ($q) use ($senderId, $recipientId) {
            $q->where('user_id_1', $recipientId)->where('user_id_2', $senderId);
        })->first();

        if ($conversation) {
            // Increment unread count for recipient
            if ($conversation->user_id_1 === $recipientId) {
                $conversation->increment('unread_count_1');
            } else {
                $conversation->increment('unread_count_2');
            }

            $conversation->update([
                'last_message' => $lastMessage,
                'last_message_at' => now(),
            ]);
        } else {
            Conversation::create([
                'user_id_1' => min($senderId, $recipientId),
                'user_id_2' => max($senderId, $recipientId),
                'last_message' => $lastMessage,
                'last_message_at' => now(),
                'unread_count_' . ($senderId < $recipientId ? 2 : 1) => 1,
            ]);
        }
    }

    /**
     * Get conversation messages
     */
    public function getConversationMessages($userId, $otherUserId, $limit = 50, $offset = 0)
    {
        return Message::where(function ($q) use ($userId, $otherUserId) {
            $q->where('sender_id', $userId)->where('recipient_id', $otherUserId);
        })->orWhere(function ($q) use ($userId, $otherUserId) {
            $q->where('sender_id', $otherUserId)->where('recipient_id', $userId);
        })->latest()
            ->limit($limit)
            ->offset($offset)
            ->get()
            ->reverse()
            ->values();
    }

    /**
     * Mark messages as read
     */
    public function markMessagesAsRead($recipientId, $senderId)
    {
        Message::where('recipient_id', $recipientId)
            ->where('sender_id', $senderId)
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);

        // Reset unread count in conversation
        $conversation = Conversation::where(function ($q) use ($recipientId, $senderId) {
            $q->where('user_id_1', $recipientId)->where('user_id_2', $senderId);
        })->orWhere(function ($q) use ($recipientId, $senderId) {
            $q->where('user_id_1', $senderId)->where('user_id_2', $recipientId);
        })->first();

        if ($conversation) {
            if ($conversation->user_id_1 === $recipientId) {
                $conversation->update(['unread_count_1' => 0]);
            } else {
                $conversation->update(['unread_count_2' => 0]);
            }
        }
    }

    /**
     * Get user conversations
     */
    public function getUserConversations($userId, $limit = 20)
    {
        return Conversation::where('user_id_1', $userId)
            ->orWhere('user_id_2', $userId)
            ->latest('last_message_at')
            ->limit($limit)
            ->get();
    }

    /**
     * Get unread count
     */
    public function getUnreadCount($userId)
    {
        $count = 0;

        Conversation::where('user_id_1', $userId)
            ->each(function ($conv) use (&$count) {
                $count += $conv->unread_count_1;
            });

        Conversation::where('user_id_2', $userId)
            ->each(function ($conv) use (&$count) {
                $count += $conv->unread_count_2;
            });

        return $count;
    }

    /**
     * Delete message (soft delete)
     */
    public function deleteMessage($messageId, $userId)
    {
        $message = Message::find($messageId);

        if (!$message || ($message->sender_id !== $userId && $message->recipient_id !== $userId)) {
            return ['success' => false, 'error' => 'Message not found'];
        }

        $message->delete();

        return ['success' => true];
    }
}
