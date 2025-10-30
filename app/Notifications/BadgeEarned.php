<?php

namespace App\Notifications;

use App\Models\Badge;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BadgeEarned extends Notification
{
    use Queueable;

    protected $badge;
    protected $points;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Badge $badge, $points)
    {
        $this->badge = $badge;
        $this->points = $points;
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
        return (new MailMessage)
            ->subject("New Badge Earned - {$this->badge->name}")
            ->greeting("Hello {$notifiable->name},")
            ->line("Congratulations! You have earned the '{$this->badge->name}' badge.")
            ->line("You've been awarded {$this->points} points.")
            ->line($this->badge->description)
            ->action('View Badge', url('/volunteer/badges'))
            ->line('Keep up the great work!');
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
            'badge_id' => $this->badge->id,
            'badge_name' => $this->badge->name,
            'points' => $this->points,
            'type' => 'badge_earned',
        ];
    }
}