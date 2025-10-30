<?php

namespace App\Http\Controllers\Organization;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\Event;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MessageController extends Controller
{
    /**
     * Display messages for an event.
     */
    public function index(Event $event)
    {
        $this->authorize('view', $event);

        // Get messages for this event
        $messages = Message::with(['sender', 'recipient'])
            ->where('event_id', $event->id)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        // Get event participants (approved volunteers)
        $participants = $event->approvedApplications()
            ->with('user')
            ->get()
            ->pluck('user')
            ->unique('id')
            ->values();

        return view('organization.messages.index', compact('event', 'messages', 'participants'));
    }

    /**
     * Send a new message to event participants.
     */
    public function store(Request $request, Event $event)
    {
        $this->authorize('update', $event);

        $validated = $request->validate([
            'recipient_ids' => 'required|array',
            'recipient_ids.*' => 'exists:users,id',
            'content' => 'required|string|max:1000',
        ]);

        // Send message to each recipient
        foreach ($validated['recipient_ids'] as $recipientId) {
            Message::create([
                'sender_id' => Auth::id(),
                'recipient_id' => $recipientId,
                'event_id' => $event->id,
                'organization_id' => $event->organization_id,
                'content' => $validated['content'],
            ]);
        }

        return back()->with('success', 'Message sent successfully to selected participants!');
    }

    /**
     * Mark a message as read.
     */
    public function markAsRead(Message $message)
    {
        // Only recipient can mark as read
        if ($message->recipient_id !== Auth::id()) {
            abort(403);
        }

        $message->update([
            'is_read' => true,
            'read_at' => now(),
        ]);

        return response()->json(['success' => true]);
    }

    /**
     * Get unread message count for a user.
     */
    public function unreadCount()
    {
        $count = Message::where('recipient_id', Auth::id())
            ->where('is_read', false)
            ->count();

        return response()->json(['count' => $count]);
    }
}