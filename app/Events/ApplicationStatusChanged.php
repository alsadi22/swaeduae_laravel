<?php

namespace App\Events;

use App\Models\Application;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ApplicationStatusChanged implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $application;
    public $previousStatus;

    /**
     * Create a new event instance.
     */
    public function __construct(Application $application, string $previousStatus)
    {
        $this->application = $application;
        $this->previousStatus = $previousStatus;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('user.' . $this->application->user_id),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'application.status.changed';
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'application_id' => $this->application->id,
            'event_id' => $this->application->event_id,
            'event_title' => $this->application->event->title,
            'status' => $this->application->status,
            'previous_status' => $this->previousStatus,
            'message' => $this->getStatusMessage(),
            'timestamp' => now()->toISOString(),
        ];
    }

    /**
     * Get user-friendly status message.
     */
    private function getStatusMessage(): string
    {
        return match($this->application->status) {
            'approved' => "Your application for '{$this->application->event->title}' has been approved!",
            'rejected' => "Your application for '{$this->application->event->title}' was not accepted this time.",
            'pending' => "Your application for '{$this->application->event->title}' is under review.",
            'withdrawn' => "You have withdrawn your application for '{$this->application->event->title}'.",
            default => "Your application status has been updated."
        };
    }
}