<?php

namespace App\Mail;

use App\Models\Application;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ApplicationStatusChanged extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $application;
    public $previousStatus;

    /**
     * Create a new message instance.
     */
    public function __construct(Application $application, string $previousStatus)
    {
        $this->application = $application;
        $this->previousStatus = $previousStatus;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subject = match($this->application->status) {
            'approved' => 'Your volunteer application has been approved!',
            'rejected' => 'Update on your volunteer application',
            default => 'Your volunteer application status has been updated'
        };

        return new Envelope(
            subject: $subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.application-status-changed',
            with: [
                'application' => $this->application,
                'previousStatus' => $this->previousStatus,
                'volunteer' => $this->application->user,
                'event' => $this->application->event,
                'organization' => $this->application->event->organization,
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}