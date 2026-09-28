<?php

namespace App\Mail;

use App\Models\AppSetting;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AdminDirectMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $emailSubject;
    public string $emailBody;
    public string $recipientName;
    public string $senderName;
    public string $appName;

    /**
     * Create a new message instance.
     */
    public function __construct(string $emailSubject, string $emailBody, string $recipientName = '', string $senderName = 'Admin KucingMu')
    {
        $this->emailSubject = $emailSubject;
        $this->emailBody = $emailBody;
        $this->recipientName = $recipientName;
        $this->senderName = $senderName;
        $this->appName = AppSetting::get('app_name', config('app.name', 'KucingMu'));
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "[{$this->appName}] {$this->emailSubject}",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.admin-direct',
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
