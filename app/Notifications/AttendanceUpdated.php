<?php

namespace App\Notifications;

use App\Models\Attendance;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AttendanceUpdated extends Notification
{
    use Queueable;

    protected $attendance;
    protected $type;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Attendance $attendance, $type)
    {
        $this->attendance = $attendance;
        $this->type = $type; // 'checkin', 'checkout', 'verified'
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
        $eventTitle = $this->attendance->event->title;
        $actionUrl = url('/volunteer/attendance/' . $this->attendance->id);
        
        switch ($this->type) {
            case 'checkin':
                $subject = "Check-in Confirmation - {$eventTitle}";
                $line = "You have been successfully checked in to the event '{$eventTitle}'.";
                break;
            case 'checkout':
                $subject = "Check-out Confirmation - {$eventTitle}";
                $line = "You have been successfully checked out from the event '{$eventTitle}'.";
                break;
            case 'verified':
                $subject = "Attendance Verified - {$eventTitle}";
                $line = "Your attendance for the event '{$eventTitle}' has been verified by the organizer.";
                break;
            default:
                $subject = "Attendance Update - {$eventTitle}";
                $line = "Your attendance status has been updated for the event '{$eventTitle}'.";
        }
        
        return (new MailMessage)
            ->subject($subject)
            ->greeting("Hello {$notifiable->name},")
            ->line($line)
            ->action('View Attendance Details', $actionUrl)
            ->line('Thank you for participating!');
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
            'attendance_id' => $this->attendance->id,
            'event_title' => $this->attendance->event->title,
            'type' => 'attendance_updated',
            'subtype' => $this->type,
        ];
    }
}