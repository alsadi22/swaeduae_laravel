<?php

namespace App\Notifications;

use App\Models\Event;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EventUpdate extends Notification
{
    use Queueable;

    protected $event;
    protected $updateType;
    protected $message;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Event $event, $updateType, $message = null)
    {
        $this->event = $event;
        $this->updateType = $updateType;
        $this->message = $message;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        $channels = ['database'];
        
        // Check user's notification preferences
        if (isset($notifiable->notification_preferences['email']) && 
            $notifiable->notification_preferences['email']) {
            $channels[] = 'mail';
        }
        
        return $channels;
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $actionUrl = url('/events/' . $this->event->id);
        
        switch ($this->updateType) {
            case 'new':
                $subject = "New Event Posted - {$this->event->title}";
                $line = "A new event '{$this->event->title}' has been posted that might interest you.";
                break;
            case 'updated':
                $subject = "Event Updated - {$this->event->title}";
                $line = "The event '{$this->event->title}' has been updated.";
                break;
            case 'cancelled':
                $subject = "Event Cancelled - {$this->event->title}";
                $line = "The event '{$this->event->title}' has been cancelled.";
                break;
            default:
                $subject = "Event Update - {$this->event->title}";
                $line = "There is an update for the event '{$this->event->title}'.";
        }
        
        $mail = (new MailMessage)
            ->subject($subject)
            ->greeting("Hello {$notifiable->name},")
            ->line($line);
            
        if ($this->message) {
            $mail->line("Details: {$this->message}");
        }
            
        return $mail->action('View Event', $actionUrl)
            ->line('Thank you for your interest!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'event_id' => $this->event->id,
            'event_title' => $this->event->title,
            'update_type' => $this->updateType,
            'message' => $this->message,
            'type' => 'event_update',
        ];
    }
}