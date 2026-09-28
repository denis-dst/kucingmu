<?php

namespace App\Mail;

use App\Models\AppSetting;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class KtamUpdateNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public User $user;
    public Collection|array $cats;
    public ?string $customNote;
    public string $senderName;
    public string $appName;
    public string $portalUrl;

    /**
     * Create a new message instance.
     */
    public function __construct(User $user, $cats, ?string $customNote = null, string $senderName = 'Admin KucingMu')
    {
        $this->user = $user;
        $this->cats = $cats;
        $this->customNote = $customNote;
        $this->senderName = $senderName;
        $this->appName = AppSetting::get('app_name', config('app.name', 'KucingMu'));
        $this->portalUrl = route('dashboard');
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "[{$this->appName}] Pemberitahuan Penyesuaian Nomor & Versi KTAKuMu Terbaru",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.ktam-update-notification',
            with: [
                'user' => $this->user,
                'cats' => $this->cats,
                'customNote' => $this->customNote,
                'senderName' => $this->senderName,
                'appName' => $this->appName,
                'portalUrl' => $this->portalUrl,
            ],
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
