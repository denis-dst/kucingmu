<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmailOutbox extends Model
{
    protected $table = 'email_outboxes';

    protected $fillable = [
        'sender_id',
        'contact_message_id',
        'recipient_email',
        'recipient_name',
        'subject',
        'body',
        'mail_type',
        'mailer',
        'status',
        'error_message',
        'sent_at',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
    ];

    /**
     * The admin or user who sent/triggered this email.
     */
    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    /**
     * Associated contact message if this email is a reply.
     */
    public function contactMessage(): BelongsTo
    {
        return $this->belongsTo(ContactMessage::class, 'contact_message_id');
    }

    /**
     * Human-readable status label.
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'sent' => 'Terkirim (SMTP OK)',
            'failed' => 'Gagal Terkirim',
            'queued' => 'Dalam Antrian',
            default => ucfirst($this->status),
        };
    }

    /**
     * Tailwind CSS class for status badge.
     */
    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'sent' => 'bg-emerald-50 text-emerald-800 border-emerald-200',
            'failed' => 'bg-rose-50 text-rose-800 border-rose-200',
            'queued' => 'bg-amber-50 text-amber-800 border-amber-200',
            default => 'bg-slate-100 text-slate-700 border-slate-200',
        };
    }

    /**
     * Human-readable email type label.
     */
    public function getTypeLabelAttribute(): string
    {
        return match ($this->mail_type) {
            'direct_compose' => 'Tulis Langsung',
            'contact_reply' => 'Balasan Kontak',
            'ktam_broadcast' => 'Mailing List KTA',
            'event_broadcast' => 'Broadcast Event',
            'general_broadcast' => 'Broadcast Umum',
            'registration_notice' => 'Notifikasi Akun',
            'test_smtp' => 'Tes Koneksi SMTP',
            'system' => 'Sistem Otomatis',
            default => ucfirst(str_replace('_', ' ', $this->mail_type)),
        };
    }

    /**
     * Tailwind CSS class for type badge.
     */
    public function getTypeBadgeClassAttribute(): string
    {
        return match ($this->mail_type) {
            'direct_compose' => 'bg-indigo-50 text-indigo-800 border-indigo-200',
            'contact_reply' => 'bg-teal-50 text-teal-800 border-teal-200',
            'ktam_broadcast' => 'bg-amber-50 text-amber-900 border-amber-300 font-bold',
            'event_broadcast' => 'bg-emerald-50 text-emerald-900 border-emerald-300 font-bold',
            'general_broadcast' => 'bg-sky-50 text-sky-900 border-sky-300 font-bold',
            'registration_notice' => 'bg-cyan-50 text-cyan-800 border-cyan-200',
            'test_smtp' => 'bg-purple-50 text-purple-800 border-purple-200',
            default => 'bg-slate-100 text-slate-700 border-slate-200',
        };
    }

    /**
     * Static helper to log an outgoing email attempt.
     */
    public static function logOutbox(array $data): ?self
    {
        try {
            $subject = (string) ($data['subject'] ?? 'Pesan KucingMu');
            $recipientName = !empty($data['recipient_name']) ? (string) $data['recipient_name'] : null;
            $errorMessage = !empty($data['error_message']) ? (string) $data['error_message'] : null;

            return self::create([
                'sender_id' => $data['sender_id'] ?? null,
                'contact_message_id' => $data['contact_message_id'] ?? null,
                'recipient_email' => mb_substr((string) ($data['recipient_email'] ?? ''), 0, 255),
                'recipient_name' => $recipientName ? mb_substr($recipientName, 0, 255) : null,
                'subject' => mb_substr($subject, 0, 255),
                'body' => (string) ($data['body'] ?? ''),
                'mail_type' => mb_substr((string) ($data['mail_type'] ?? 'direct_compose'), 0, 50),
                'mailer' => mb_substr((string) ($data['mailer'] ?? config('mail.default', 'smtp')), 0, 50),
                'status' => in_array($data['status'] ?? 'sent', ['sent', 'failed', 'queued']) ? $data['status'] : 'sent',
                'error_message' => $errorMessage ? mb_substr($errorMessage, 0, 2000) : null,
                'sent_at' => ($data['status'] ?? 'sent') === 'sent' ? now() : null,
            ]);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Gagal mencatat log EmailOutbox: ' . $e->getMessage());
            return null;
        }
    }
}
