<?php

namespace App\Notifications;

use App\Models\Certificate;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CertificateIssued extends Notification
{
    use Queueable;

    protected $certificate;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Certificate $certificate)
    {
        $this->certificate = $certificate;
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
        $eventTitle = $this->certificate->event->title;
        $actionUrl = url('/volunteer/certificates/' . $this->certificate->id);
        
        return (new MailMessage)
            ->subject("Certificate Issued - {$eventTitle}")
            ->greeting("Hello {$notifiable->name},")
            ->line("Congratulations! You have been issued a certificate for your participation in the event '{$eventTitle}'.")
            ->line("Certificate Number: {$this->certificate->certificate_number}")
            ->line("Hours Completed: {$this->certificate->hours_completed}")
            ->action('View Certificate', $actionUrl)
            ->line('You can download and share your certificate.');
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
            'certificate_id' => $this->certificate->id,
            'event_title' => $this->certificate->event->title,
            'certificate_number' => $this->certificate->certificate_number,
            'type' => 'certificate_issued',
        ];
    }
}