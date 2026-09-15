<?php

namespace App\Mail;

use App\Models\AppSetting;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RegistrationSuccessMail extends Mailable
{
    use Queueable, SerializesModels;

    public User $user;
    public string $appName;
    public ?string $logoPath;
    public string $loginUrl;

    /**
     * Create a new message instance.
     */
    public function __construct(User $user)
    {
        $this->user = $user;

        // Retrieve dynamic app name and logo from cache
        $this->appName = AppSetting::get('app_name', config('app.name', 'KucingMu'));

        $logoSetting = AppSetting::get('app_logo');
        $resolvedLogo = null;

        if ($logoSetting && file_exists(storage_path('app/public/' . $logoSetting))) {
            $resolvedLogo = storage_path('app/public/' . $logoSetting);
        } elseif ($logoSetting && file_exists(public_path('storage/' . $logoSetting))) {
            $resolvedLogo = public_path('storage/' . $logoSetting);
        }

        $this->logoPath = $resolvedLogo;
        $this->loginUrl = route('login');
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Selamat Datang di {$this->appName} - Pendaftaran Akun Berhasil",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.registration-success',
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
