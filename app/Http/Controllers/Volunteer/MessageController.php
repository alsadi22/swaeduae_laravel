<?php

namespace App\Http\Controllers\Volunteer;

use App\Http\Controllers\Controller;
use App\Services\MessagingService;
use App\Models\Conversation;
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
     * List conversations
     */
    public function conversations()
    {
        $conversations = $this->messagingService->getUserConversations(Auth::id(), 20);

        return view('volunteer.messages.conversations', compact('conversations'));
    }

    /**
     * Show conversation
     */
    public function show($userId)
    {
        $messages = $this->messagingService->getConversationMessages(Auth::id(), $userId, 50);
        
        // Mark messages as read
        $this->messagingService->markMessagesAsRead(Auth::id(), $userId);

        $otherUser = \App\Models\User::findOrFail($userId);

        return view('volunteer.messages.show', compact('messages', 'otherUser'));
    }

    /**
     * Send message
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'recipient_id' => 'required|numeric|exists:users,id',
            'content' => 'required|string|max:5000',
        ]);

        if ($validated['recipient_id'] === Auth::id()) {
            return back()->with('error', 'Cannot send message to yourself');
        }

        $result = $this->messagingService->sendMessage(
            Auth::id(),
            $validated['recipient_id'],
            $validated['content']
        );

        if ($result['success']) {
            return back()->with('success', 'Message sent');
        }

        return back()->with('error', 'Failed to send message');
    }

    /**
     * Get unread count
     */
    public function getUnreadCount()
    {
        $count = $this->messagingService->getUnreadCount(Auth::id());

        return response()->json(['unread_count' => $count]);
    }
}
