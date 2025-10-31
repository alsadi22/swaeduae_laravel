<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\MessagingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    protected $messagingService;

    public function __construct(MessagingService $messagingService)
    {
        $this->messagingService = $messagingService;
    }

    /**
     * Get conversations
     */
    public function conversations()
    {
        $limit = request()->get('limit', 20);
        $conversations = $this->messagingService->getUserConversations(Auth::id(), $limit);

        return response()->json($conversations);
    }

    /**
     * Get conversation messages
     */
    public function messages($userId)
    {
        $limit = request()->get('limit', 50);
        $offset = request()->get('offset', 0);

        $messages = $this->messagingService->getConversationMessages(
            Auth::id(),
            $userId,
            $limit,
            $offset
        );

        return response()->json($messages);
    }

    /**
     * Send message
     */
    public function send(Request $request)
    {
        $validated = $request->validate([
            'recipient_id' => 'required|numeric|exists:users,id',
            'content' => 'required|string|max:5000',
            'type' => 'nullable|in:text,media',
            'media_url' => 'nullable|url',
        ]);

        if ($validated['recipient_id'] === Auth::id()) {
            return response()->json(['error' => 'Cannot send to yourself'], 422);
        }

        $result = $this->messagingService->sendMessage(
            Auth::id(),
            $validated['recipient_id'],
            $validated['content'],
            $validated['type'] ?? 'text',
            $validated['media_url'] ?? null
        );

        if ($result['success']) {
            return response()->json($result, 201);
        }

        return response()->json(['error' => $result['error']], 400);
    }

    /**
     * Mark as read
     */
    public function markAsRead($userId)
    {
        $this->messagingService->markMessagesAsRead(Auth::id(), $userId);

        return response()->json(['success' => true]);
    }

    /**
     * Get unread count
     */
    public function unreadCount()
    {
        $count = $this->messagingService->getUnreadCount(Auth::id());

        return response()->json(['unread_count' => $count]);
    }

    /**
     * Delete message
     */
    public function delete($messageId)
    {
        $result = $this->messagingService->deleteMessage($messageId, Auth::id());

        if ($result['success']) {
            return response()->json($result);
        }

        return response()->json(['error' => $result['error']], 400);
    }
}
