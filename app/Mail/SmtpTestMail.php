<?php

namespace App\Mail;

use App\Models\AppSetting;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SmtpTestMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $appName;
    public string $testerName;
    public array $smtpDetails;

    /**
     * Create a new message instance.
     */
    public function __construct(string $testerName, array $smtpDetails)
    {
        $this->testerName = $testerName;
        $this->smtpDetails = $smtpDetails;
        $this->appName = AppSetting::get('app_name', config('app.name', 'KucingMu'));
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "✅ [{$this->appName}] Uji Coba Konfigurasi SMTP Berhasil",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.smtp-test',
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
