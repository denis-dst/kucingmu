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
    public ?string $initialPassword;
    public ?string $catName;

    /**
     * Create a new message instance.
     */
    public function __construct(User $user, ?string $initialPassword = null, ?string $catName = null)
    {
        $this->user = $user;
        $this->initialPassword = $initialPassword;
        $this->catName = $catName;

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
        $subject = !empty($this->catName)
            ? "Selamat Datang di {$this->appName} - Pendaftaran Kucing {$this->catName} Berhasil"
            : "Selamat Datang di {$this->appName} - Pendaftaran Akun Berhasil";

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
