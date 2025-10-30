<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\ScheduledReport;

class ScheduledReportMail extends Mailable
{
    use Queueable, SerializesModels;

    public $scheduledReport;
    public $reportContent;
    public $filename;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(ScheduledReport $scheduledReport, $reportContent, $filename)
    {
        $this->scheduledReport = $scheduledReport;
        $this->reportContent = $reportContent;
        $this->filename = $filename;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $format = $this->scheduledReport->format;
        $extension = $format === 'excel' ? 'xlsx' : ($format === 'csv' ? 'csv' : 'pdf');
        
        return $this->subject("Scheduled Report: {$this->scheduledReport->name}")
            ->view('emails.scheduled-report')
            ->attachData($this->reportContent, "{$this->filename}.{$extension}", [
                'mime' => $this->getMimeType($format),
            ]);
    }

    /**
     * Get the MIME type for the attachment based on format.
     *
     * @param string $format
     * @return string
     */
    private function getMimeType($format)
    {
        switch ($format) {
            case 'excel':
                return 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
            case 'csv':
                return 'text/csv';
            case 'pdf':
            default:
                return 'application/pdf';
        }
    }
}