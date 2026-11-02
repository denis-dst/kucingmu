<?php

namespace Tests\Feature;

use App\Jobs\SendOutboxEmailJob;
use App\Mail\AdminDirectMail;
use App\Mail\ContactResponseMail;
use App\Models\ContactMessage;
use App\Models\EmailOutbox;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class AdminMailboxQueueTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'name' => 'Administrator KucingMu',
            'email' => 'admin@kucingmu.online',
            'role' => 'admin',
            'roles' => ['admin'],
        ]);
    }

    /**
     * Test tampilan outbox dengan status failed dan statistik antrian.
     */
    public function test_outbox_displays_failed_status_filter_and_queue_stats(): void
    {
        EmailOutbox::create([
            'sender_id' => $this->admin->id,
            'recipient_email' => 'gagal1@example.com',
            'recipient_name' => 'Penerima Gagal 1',
            'subject' => 'Pemberitahuan Agenda Gagal 1',
            'body' => 'Isi pesan email yang gagal dikirimkan.',
            'mail_type' => 'event_broadcast',
            'status' => 'failed',
            'error_message' => 'Connection to mail.kucingmu.online timed out',
        ]);

        EmailOutbox::create([
            'sender_id' => $this->admin->id,
            'recipient_email' => 'sukses@example.com',
            'recipient_name' => 'Penerima Sukses',
            'subject' => 'Pemberitahuan Sukses',
            'body' => 'Isi pesan email terkirim.',
            'mail_type' => 'direct_compose',
            'status' => 'sent',
            'sent_at' => now(),
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.mail.outbox', ['status' => 'failed']));

        $response->assertStatus(200);
        $response->assertSee('Pemberitahuan Agenda Gagal 1');
        $response->assertSee('Gagal Kirim');
        $response->assertSee('Masukkan Semua ke Antrian (Kirim Ulang)');
        $response->assertDontSee('Pemberitahuan Sukses');
    }

    /**
     * Test kirim ulang email tunggal masuk ke Jobs Queue tanpa error 500.
     */
    public function test_resend_outbox_dispatches_send_outbox_email_job_and_updates_status_to_queued(): void
    {
        Queue::fake();

        $outbox = EmailOutbox::create([
            'sender_id' => $this->admin->id,
            'recipient_email' => 'budi@example.com',
            'recipient_name' => 'Budi',
            'subject' => 'Undangan Agenda Komunitas',
            'body' => 'Halo Budi, silakan hadir di acara vaksinasi kucing.',
            'mail_type' => 'direct_compose',
            'status' => 'failed',
            'error_message' => '500 Internal Server Error saat pengiriman sebelumnya',
        ]);

        $response = $this->actingAs($this->admin)->post(route('admin.mail.outbox.resend', $outbox->id));

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $outbox->refresh();
        $this->assertEquals('queued', $outbox->status);
        $this->assertStringContainsString('Dimasukkan ke antrian pengiriman', (string) $outbox->error_message);

        Queue::assertPushed(SendOutboxEmailJob::class, function ($job) use ($outbox) {
            return $job->outboxId === $outbox->id;
        });
    }

    /**
     * Test kirim ulang seluruh email gagal secara massal ke dalam Jobs Queue.
     */
    public function test_resend_all_failed_dispatches_jobs_for_all_failed_records(): void
    {
        Queue::fake();

        $failed1 = EmailOutbox::create([
            'sender_id' => $this->admin->id,
            'recipient_email' => 'user1@example.com',
            'subject' => 'Broadcast Update 1',
            'body' => 'Pesan 1',
            'mail_type' => 'general_broadcast',
            'status' => 'failed',
            'error_message' => 'SMTP timeout',
        ]);

        $failed2 = EmailOutbox::create([
            'sender_id' => $this->admin->id,
            'recipient_email' => 'user2@example.com',
            'subject' => 'Broadcast Update 2',
            'body' => 'Pesan 2',
            'mail_type' => 'general_broadcast',
            'status' => 'failed',
            'error_message' => 'SMTP connection reset',
        ]);

        $sent = EmailOutbox::create([
            'sender_id' => $this->admin->id,
            'recipient_email' => 'user3@example.com',
            'subject' => 'Broadcast Update 3 (Sudah Terkirim)',
            'body' => 'Pesan 3',
            'mail_type' => 'general_broadcast',
            'status' => 'sent',
            'sent_at' => now(),
        ]);

        $response = $this->actingAs($this->admin)->post(route('admin.mail.outbox.resend-all-failed'));

        $response->assertRedirect(route('admin.mail.outbox', ['status' => 'queued']));
        $response->assertSessionHas('success');

        $this->assertEquals('queued', $failed1->fresh()->status);
        $this->assertEquals('queued', $failed2->fresh()->status);
        $this->assertEquals('sent', $sent->fresh()->status);

        Queue::assertPushed(SendOutboxEmailJob::class, 2);
    }

    /**
     * Test SendOutboxEmailJob berhasil mengirimkan direct email dan mengupdate status menjadi sent.
     */
    public function test_send_outbox_email_job_executes_and_sends_direct_mail(): void
    {
        Mail::fake();

        $outbox = EmailOutbox::create([
            'sender_id' => $this->admin->id,
            'recipient_email' => 'member@example.com',
            'recipient_name' => 'Member Setia',
            'subject' => 'Pemberitahuan Khusus',
            'body' => 'Terima kasih telah aktif di komunitas KucingMu.',
            'mail_type' => 'direct_compose',
            'status' => 'queued',
        ]);

        $job = new SendOutboxEmailJob($outbox, $this->admin->id);
        $job->handle();

        $outbox->refresh();
        $this->assertEquals('sent', $outbox->status);
        $this->assertNull($outbox->error_message);
        $this->assertNotNull($outbox->sent_at);

        Mail::assertSent(AdminDirectMail::class, function ($mail) use ($outbox) {
            return $mail->hasTo($outbox->recipient_email);
        });
    }

    /**
     * Test SendOutboxEmailJob berhasil mengirimkan balasan pesan kontak (ContactResponseMail).
     */
    public function test_send_outbox_email_job_handles_contact_reply_mail(): void
    {
        Mail::fake();

        $contact = ContactMessage::create([
            'user_id' => null,
            'name' => 'Ahmad Pengunjung',
            'email' => 'ahmad@example.com',
            'phone' => '081234567890',
            'subject' => 'Pertanyaan KTA Kucing',
            'message' => 'Bagaimana cara mendaftarkan kucing saya?',
            'status' => 'read',
        ]);

        $outbox = EmailOutbox::create([
            'sender_id' => $this->admin->id,
            'contact_message_id' => $contact->id,
            'recipient_email' => $contact->email,
            'recipient_name' => $contact->name,
            'subject' => 'Re: Pertanyaan KTA Kucing',
            'body' => 'Silakan masuk ke menu Kucing Saya lalu daftarkan data anabul Anda.',
            'mail_type' => 'contact_reply',
            'status' => 'queued',
        ]);

        $job = new SendOutboxEmailJob($outbox, $this->admin->id);
        $job->handle();

        $outbox->refresh();
        $this->assertEquals('sent', $outbox->status);
        $this->assertNull($outbox->error_message);
        $this->assertNotNull($outbox->sent_at);

        Mail::assertSent(ContactResponseMail::class, function ($mail) use ($contact) {
            return $mail->hasTo($contact->email);
        });
    }

    /**
     * Test proses antrian ketika kosong memberikan notifikasi yang ramah.
     */
    public function test_process_queue_when_empty_returns_warning(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.mail.queue.process'));

        $response->assertRedirect();
        $response->assertSessionHas('warning');
    }
}

