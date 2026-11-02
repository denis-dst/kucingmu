<?php

namespace App\Jobs;

use App\Mail\AdminDirectMail;
use App\Mail\ContactResponseMail;
use App\Models\EmailOutbox;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendOutboxEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $outboxId;
    public ?int $adminId;

    /**
     * Jumlah maksimum percobaan pengiriman.
     */
    public int $tries = 3;

    /**
     * Jeda waktu antar percobaan dalam detik (backoff).
     */
    public array $backoff = [10, 30, 60];

    /**
     * Create a new job instance.
     */
    public function __construct(int|EmailOutbox $outbox, ?int $adminId = null)
    {
        $this->outboxId = $outbox instanceof EmailOutbox ? $outbox->id : $outbox;
        $this->adminId = $adminId;
    }

    /**
     * Eksekusi pekerjaan pengiriman email via SMTP di latar belakang.
     */
    public function handle(): void
    {
        $outbox = EmailOutbox::with(['contactMessage', 'sender'])->find($this->outboxId);

        if (!$outbox) {
            Log::warning("SendOutboxEmailJob: EmailOutbox dengan ID {$this->outboxId} tidak ditemukan.");
            return;
        }

        // Jika email sudah berstatus terkirim (sent), lewati agar tidak dobel
        if ($outbox->status === 'sent') {
            Log::info("SendOutboxEmailJob: EmailOutbox ID {$outbox->id} sudah berstatus sent. Dilewati.");
            return;
        }

        $senderName = 'Admin KucingMu';
        if ($this->adminId) {
            $admin = User::find($this->adminId);
            if ($admin?->name) {
                $senderName = $admin->name;
            }
        } elseif ($outbox->sender?->name) {
            $senderName = $outbox->sender->name;
        }

        try {
            if ($outbox->mail_type === 'contact_reply' && $outbox->contactMessage) {
                Mail::to($outbox->recipient_email)->send(
                    new ContactResponseMail($outbox->contactMessage, $outbox->body, $senderName)
                );
            } else {
                Mail::to($outbox->recipient_email)->send(
                    new AdminDirectMail($outbox->subject, $outbox->body, $outbox->recipient_name ?: '', $senderName)
                );
            }

            // Pengiriman sukses: perbarui status menjadi sent
            $outbox->update([
                'status' => 'sent',
                'error_message' => null,
                'sent_at' => now(),
            ]);

            Log::info("SendOutboxEmailJob: Berhasil mengirim email ID {$outbox->id} ke {$outbox->recipient_email}");
        } catch (\Throwable $e) {
            $errorMessage = $e->getMessage();
            Log::error("SendOutboxEmailJob: Gagal mengirim email ID {$outbox->id} ke {$outbox->recipient_email}: {$errorMessage}");

            if ($this->attempts() >= $this->tries) {
                $outbox->update([
                    'status' => 'failed',
                    'error_message' => $errorMessage,
                ]);
            } else {
                $outbox->update([
                    'status' => 'queued',
                    'error_message' => "Percobaan ke-{$this->attempts()} gagal: {$errorMessage}. Menunggu antrian berikutnya.",
                ]);
            }

            throw $e;
        }
    }

    /**
     * Penanganan saat job gagal permanen setelah seluruh percobaan habis.
     */
    public function failed(?\Throwable $exception): void
    {
        $outbox = EmailOutbox::find($this->outboxId);
        if ($outbox) {
            $outbox->update([
                'status' => 'failed',
                'error_message' => $exception ? $exception->getMessage() : 'Gagal setelah batas maksimal percobaan antrian habis.',
            ]);
        }
    }
}
