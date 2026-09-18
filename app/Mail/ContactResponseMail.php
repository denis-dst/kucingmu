<?php

namespace App\Mail;

use App\Models\AppSetting;
use App\Models\ContactMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactResponseMail extends Mailable
{
    use Queueable, SerializesModels;

    public ContactMessage $contactMessage;
    public string $appName;
    public string $responseContent;
    public string $responderName;

    /**
     * Create a new message instance.
     */
    public function __construct(ContactMessage $contactMessage, string $responseContent, string $responderName)
    {
        $this->contactMessage = $contactMessage;
        $this->responseContent = $responseContent;
        $this->responderName = $responderName;
        $this->appName = AppSetting::get('app_name', config('app.name', 'KucingMu'));
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Re: [{$this->appName}] {$this->contactMessage->subject}",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.contact-response',
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
