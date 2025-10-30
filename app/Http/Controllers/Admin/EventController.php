<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Organization;
use Illuminate\Http\Request;

class EventController extends Controller
{
    /**
     * Display a listing of events.
     */
    public function index(Request $request)
    {
        $query = Event::with(['organization']);
        
        // Apply filters
        if ($request->has('search') && $request->search) {
            $query->where(function($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhereHas('organization', function($orgQuery) use ($request) {
                      $orgQuery->where('name', 'like', '%' . $request->search . '%');
                  });
            });
        }
        
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }
        
        $events = $query->latest()->paginate(15);
        
        return view('admin.events.index', compact('events'));
    }

    /**
     * Display the specified event.
     */
    public function show(Event $event)
    {
        $event->load(['organization', 'applications.user', 'certificates']);
        return view('admin.events.show', compact('event'));
    }

    /**
     * Approve the specified event.
     */
    public function approve(Event $event)
    {
        $event->update([
            'status' => 'published',
            'approved_at' => now(),
            'approved_by' => auth()->id(),
            'rejection_reason' => null,
        ]);

        return back()->with('success', 'Event approved successfully!');
    }

    /**
     * Reject the specified event.
     */
    public function reject(Request $request, Event $event)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        $event->update([
            'status' => 'rejected',
            'rejection_reason' => $request->rejection_reason,
            'approved_at' => null,
            'approved_by' => null,
        ]);

        return back()->with('success', 'Event rejected successfully!');
    }
}