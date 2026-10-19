<?php

namespace App\Mail;

use App\Models\AppSetting;
use App\Models\Event;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BroadcastAnnouncementMail extends Mailable
{
    use Queueable, SerializesModels;

    public User $user;
    public string $emailSubject;
    public string $messageBody;
    public ?Event $event;
    public string $broadcastType;
    public ?string $ctaText;
    public ?string $ctaUrl;
    public string $senderName;
    public string $appName;

    /**
     * Create a new message instance.
     */
    public function __construct(
        User $user,
        string $emailSubject,
        string $messageBody,
        ?Event $event = null,
        string $broadcastType = 'event_announcement',
        ?string $ctaText = null,
        ?string $ctaUrl = null,
        ?string $senderName = 'Admin KucingMu'
    ) {
        $this->user = $user;
        $this->emailSubject = $emailSubject;
        $this->messageBody = $messageBody;
        $this->event = $event;
        $this->broadcastType = $broadcastType;
        $this->appName = AppSetting::get('app_name', config('app.name', 'KucingMu'));
        $this->senderName = $senderName ?: 'Admin KucingMu';

        if ($this->event) {
            $this->ctaText = $ctaText ?: 'Lihat Detail & Daftar Kegiatan';
            $this->ctaUrl = $ctaUrl ?: route('events.show', $this->event);
        } else {
            $this->ctaText = $ctaText ?: 'Buka Portal KucingMu';
            $this->ctaUrl = $ctaUrl ?: route('dashboard');
        }
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
            view: 'emails.broadcast-announcement',
            with: [
                'user' => $this->user,
                'emailSubject' => $this->emailSubject,
                'messageBody' => $this->messageBody,
                'event' => $this->event,
                'broadcastType' => $this->broadcastType,
                'ctaText' => $this->ctaText,
                'ctaUrl' => $this->ctaUrl,
                'senderName' => $this->senderName,
                'appName' => $this->appName,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        return [];
    }
}
