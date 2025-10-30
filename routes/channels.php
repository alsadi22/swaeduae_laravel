<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// Private user channel for personal notifications
Broadcast::channel('user.{userId}', function ($user, $userId) {
    return (int) $user->id === (int) $userId;
});

// Public events channel for new event notifications
Broadcast::channel('events', function () {
    return true; // Public channel
});

// Organization channel for organization-specific updates
Broadcast::channel('organization.{organizationId}', function ($user, $organizationId) {
    return $user->organizations->contains($organizationId);
});

// Event-specific channel for event updates
Broadcast::channel('event.{eventId}', function ($user, $eventId) {
    // Check if user is either:
    // 1. Applied to this event
    // 2. Member of the organization that owns this event
    // 3. Admin
    
    if ($user->hasRole('admin')) {
        return true;
    }
    
    // Check if user applied to this event
    if ($user->applications()->where('event_id', $eventId)->exists()) {
        return true;
    }
    
    // Check if user is member of organization that owns this event
    $event = \App\Models\Event::find($eventId);
    if ($event && $user->organizations->contains($event->organization_id)) {
        return true;
    }
    
    return false;
});

// Admin channel for administrative notifications
Broadcast::channel('admin', function ($user) {
    return $user->hasRole('admin');
});