<?php

namespace App\Http\Controllers\Organization;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AnnouncementController extends Controller
{
    /**
     * Display a listing of announcements for an event.
     */
    public function index(Event $event)
    {
        $this->authorize('view', $event);

        $announcements = Announcement::where('event_id', $event->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('organization.announcements.index', compact('event', 'announcements'));
    }

    /**
     * Show the form for creating a new announcement.
     */
    public function create(Event $event)
    {
        $this->authorize('update', $event);

        return view('organization.announcements.create', compact('event'));
    }

    /**
     * Store a newly created announcement.
     */
    public function store(Request $request, Event $event)
    {
        $this->authorize('update', $event);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'type' => 'required|in:general,urgent,update',
            'is_published' => 'boolean',
        ]);

        $validated['event_id'] = $event->id;
        $validated['organization_id'] = $event->organization_id;
        $validated['created_by'] = Auth::id();
        
        if ($request->boolean('is_published')) {
            $validated['published_at'] = now();
        }

        $announcement = Announcement::create($validated);

        return redirect()->route('organization.announcements.index', $event)
            ->with('success', 'Announcement created successfully!');
    }

    /**
     * Show the form for editing the specified announcement.
     */
    public function edit(Event $event, Announcement $announcement)
    {
        $this->authorize('update', $event);

        return view('organization.announcements.edit', compact('event', 'announcement'));
    }

    /**
     * Update the specified announcement.
     */
    public function update(Request $request, Event $event, Announcement $announcement)
    {
        $this->authorize('update', $event);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'type' => 'required|in:general,urgent,update',
            'is_published' => 'boolean',
        ]);

        if ($request->boolean('is_published') && !$announcement->is_published) {
            $validated['published_at'] = now();
        }

        $announcement->update($validated);

        return redirect()->route('organization.announcements.index', $event)
            ->with('success', 'Announcement updated successfully!');
    }

    /**
     * Remove the specified announcement.
     */
    public function destroy(Event $event, Announcement $announcement)
    {
        $this->authorize('update', $event);

        $announcement->delete();

        return redirect()->route('organization.announcements.index', $event)
            ->with('success', 'Announcement deleted successfully!');
    }

    /**
     * Publish an announcement.
     */
    public function publish(Event $event, Announcement $announcement)
    {
        $this->authorize('update', $event);

        $announcement->update([
            'is_published' => true,
            'published_at' => now(),
        ]);

        return back()->with('success', 'Announcement published successfully!');
    }

    /**
     * Unpublish an announcement.
     */
    public function unpublish(Event $event, Announcement $announcement)
    {
        $this->authorize('update', $event);

        $announcement->update([
            'is_published' => false,
            'published_at' => null,
        ]);

        return back()->with('success', 'Announcement unpublished successfully!');
    }
}