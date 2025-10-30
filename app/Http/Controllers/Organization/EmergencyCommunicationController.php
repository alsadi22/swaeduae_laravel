<?php

namespace App\Http\Controllers\Organization;

use App\Http\Controllers\Controller;
use App\Models\EmergencyCommunication;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EmergencyCommunicationController extends Controller
{
    /**
     * Display a listing of emergency communications for an event.
     */
    public function index(Event $event)
    {
        $this->authorize('view', $event);

        $emergencyCommunications = EmergencyCommunication::where('event_id', $event->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('organization.emergency-communications.index', compact('event', 'emergencyCommunications'));
    }

    /**
     * Show the form for creating a new emergency communication.
     */
    public function create(Event $event)
    {
        $this->authorize('update', $event);

        // Get event participants for recipient selection
        $participants = $event->approvedApplications()
            ->with('user')
            ->get()
            ->pluck('user')
            ->unique('id')
            ->values();

        return view('organization.emergency-communications.create', compact('event', 'participants'));
    }

    /**
     * Store a newly created emergency communication.
     */
    public function store(Request $request, Event $event)
    {
        $this->authorize('update', $event);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string|max:2000',
            'priority' => 'required|in:low,normal,high,critical',
            'send_sms' => 'boolean',
            'send_email' => 'boolean',
            'send_push' => 'boolean',
            'recipient_type' => 'required|in:all,selected',
            'recipient_ids' => 'nullable|array',
            'recipient_ids.*' => 'exists:users,id',
        ]);

        // Prepare recipient filters
        $recipientFilters = [
            'type' => $validated['recipient_type'],
            'recipients' => $validated['recipient_type'] === 'selected' ? $validated['recipient_ids'] ?? [] : [],
        ];

        $emergencyCommunication = EmergencyCommunication::create([
            'event_id' => $event->id,
            'organization_id' => $event->organization_id,
            'created_by' => Auth::id(),
            'title' => $validated['title'],
            'content' => $validated['content'],
            'priority' => $validated['priority'],
            'send_sms' => $request->boolean('send_sms'),
            'send_email' => $request->boolean('send_email'),
            'send_push' => $request->boolean('send_push'),
            'recipient_filters' => $recipientFilters,
            'sent_at' => now(),
        ]);

        // Send the communication (in a real implementation, this would integrate with SMS/email services)
        $this->sendCommunication($emergencyCommunication, $event);

        return redirect()->route('organization.emergency-communications.index', $event)
            ->with('success', 'Emergency communication sent successfully!');
    }

    /**
     * Display the specified emergency communication.
     */
    public function show(Event $event, EmergencyCommunication $emergencyCommunication)
    {
        $this->authorize('view', $event);

        return view('organization.emergency-communications.show', compact('event', 'emergencyCommunication'));
    }

    /**
     * Remove the specified emergency communication.
     */
    public function destroy(Event $event, EmergencyCommunication $emergencyCommunication)
    {
        $this->authorize('update', $event);

        $emergencyCommunication->delete();

        return redirect()->route('organization.emergency-communications.index', $event)
            ->with('success', 'Emergency communication deleted successfully!');
    }

    /**
     * Send the emergency communication.
     */
    private function sendCommunication(EmergencyCommunication $emergencyCommunication, Event $event)
    {
        // In a real implementation, this would integrate with actual SMS/email services
        // For now, we'll just log that the communication was sent
        
        // Get recipients based on filters
        $recipients = collect();
        
        if ($emergencyCommunication->recipient_filters['type'] === 'all') {
            // Get all approved participants
            $recipients = $event->approvedApplications()
                ->with('user')
                ->get()
                ->pluck('user')
                ->unique('id')
                ->values();
        } else {
            // Get selected recipients
            $recipientIds = $emergencyCommunication->recipient_filters['recipients'] ?? [];
            $recipients = \App\Models\User::whereIn('id', $recipientIds)->get();
        }

        // Log the communication (in a real implementation, this would send actual messages)
        \Log::info('Emergency communication sent', [
            'communication_id' => $emergencyCommunication->id,
            'event_id' => $event->id,
            'recipients_count' => $recipients->count(),
            'priority' => $emergencyCommunication->priority,
            'channels' => [
                'sms' => $emergencyCommunication->send_sms,
                'email' => $emergencyCommunication->send_email,
                'push' => $emergencyCommunication->send_push,
            ],
        ]);

        // In a real implementation, you would:
        // 1. Send SMS via a service like Twilio
        // 2. Send emails via Laravel's mail system
        // 3. Send push notifications via Laravel Echo or a push service
    }
}