<?php

namespace App\Notifications;

use App\Models\Application;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ApplicationStatusUpdated extends Notification
{
    use Queueable;

    protected $application;
    protected $status;
    protected $reason;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Application $application, $status, $reason = null)
    {
        $this->application = $application;
        $this->status = $status;
        $this->reason = $reason;
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
        $statusText = ucfirst($this->status);
        $actionUrl = url('/volunteer/applications/' . $this->application->id);
        
        return (new MailMessage)
            ->subject("Application {$statusText} - {$this->application->event->title}")
            ->greeting("Hello {$notifiable->name},")
            ->line("Your application for the event '{$this->application->event->title}' has been {$statusText}.")
            ->when($this->reason, function ($mail) {
                $mail->line("Reason: {$this->reason}");
            })
            ->action('View Application', $actionUrl)
            ->line('Thank you for your interest in volunteering!');
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
            'application_id' => $this->application->id,
            'event_title' => $this->application->event->title,
            'status' => $this->status,
            'reason' => $this->reason,
            'type' => 'application_status_updated',
        ];
    }
}