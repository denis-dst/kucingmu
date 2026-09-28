<?php

namespace App\Console\Commands;

use App\Mail\KtamUpdateNotificationMail;
use App\Models\Cat;
use App\Models\EmailOutbox;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class BroadcastKtamUpdateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mail:broadcast-ktam-update 
                            {--wilayah= : Filter kode wilayah (misal: 11, 34)}
                            {--test-email= : Kirim hanya 1 email uji coba ke alamat ini}
                            {--note= : Catatan tambahan kustom untuk member}
                            {--limit= : Batasi jumlah member yang dikirimi}
                            {--dry-run : Simulasi daftar penerima tanpa mengirim email}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Kirim email mailing list/broadcast via SMTP ke seluruh member pemilik KTAKuMu terbit untuk pengecekan versi kartu terbaru';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $wilayahFilter = $this->option('wilayah');
        $testEmail = $this->option('test-email');
        $customNote = $this->option('note');
        $limit = $this->option('limit') ? (int) $this->option('limit') : null;
        $dryRun = (bool) $this->option('dry-run');

        $this->info("==========================================================================");
        $this->info("  KucingMu - Mailing List / Broadcast KTAKuMu Update via SMTP");
        $this->info("==========================================================================");

        $catsQuery = Cat::with(['owner', 'ktamCard'])
            ->whereNotNull('unique_code')
            ->where('unique_code', '!=', '')
            ->whereHas('owner');

        if (!empty($wilayahFilter) && $wilayahFilter !== 'all') {
            $catsQuery->where('wilayah_code', $wilayahFilter);
            $this->warn("Filter aktif: Wilayah Kode = {$wilayahFilter}");
        }

        $issuedCats = $catsQuery->get();

        if ($issuedCats->isEmpty()) {
            $this->error("Tidak ditemukan data kucing dengan KTAKuMu terbit sesuai kriteria.");
            return Command::FAILURE;
        }

        $groupedByOwner = $issuedCats->groupBy('user_id');

        if ($limit && $limit > 0) {
            $groupedByOwner = $groupedByOwner->take($limit);
            $this->warn("Limit aktif: Dibatasi maksimal {$limit} pemilik.");
        }

        $this->info("Total kucing ber-KTA: {$issuedCats->count()} | Total pemilik (penerima): {$groupedByOwner->count()}");

        $previewRows = [];
        foreach ($groupedByOwner as $userId => $cats) {
            $owner = $cats->first()->owner;
            $catNames = $cats->pluck('name')->implode(', ');
            $catCodes = $cats->pluck('unique_code')->implode(', ');

            $previewRows[] = [
                'User ID' => $owner ? $owner->id : $userId,
                'Nama Pemilik' => $owner ? $owner->name : '-',
                'Email' => $testEmail ?: ($owner ? $owner->email : '-'),
                'Jumlah Kucing' => $cats->count(),
                'Nama Kucing' => $catNames,
                'Nomor KTAKuMu' => $catCodes,
            ];
        }

        $this->table(['User ID', 'Nama Pemilik', 'Email Tujuan', 'Jml Kucing', 'Nama Anabul', 'Nomor KTAKuMu'], array_slice($previewRows, 0, 25));

        if (count($previewRows) > 25) {
            $this->info("... dan " . (count($previewRows) - 25) . " penerima lainnya.");
        }

        if ($dryRun) {
            $this->warn("[DRY-RUN] Mode simulasi. Tidak ada email yang dikirim.");
            return Command::SUCCESS;
        }

        if (!$this->confirm("Apakah Anda yakin ingin mengirim email broadcast ke " . count($previewRows) . " penerima di atas melalui SMTP?", true)) {
            $this->warn("Pengiriman dibatalkan.");
            return Command::FAILURE;
        }

        $this->info("Memulai pengiriman email via SMTP...");
        $bar = $this->output->createProgressBar(count($groupedByOwner));
        $bar->start();

        $sentCount = 0;
        $failedCount = 0;
        $systemAdmin = User::whereIn('role', ['superadmin', 'admin'])->first();
        $adminId = $systemAdmin ? $systemAdmin->id : null;
        $adminName = $systemAdmin ? $systemAdmin->name : 'Admin KucingMu';

        foreach ($groupedByOwner as $userId => $cats) {
            $owner = $cats->first()->owner;
            if (!$owner || empty($owner->email) || !filter_var($owner->email, FILTER_VALIDATE_EMAIL)) {
                $bar->advance();
                continue;
            }

            $targetEmail = $testEmail ?: $owner->email;
            $catNames = $cats->pluck('name')->implode(', ');
            $catCodes = $cats->pluck('unique_code')->implode(', ');
            $subject = "[KucingMu] Pemberitahuan Penyesuaian Nomor & Versi KTAKuMu ({$catNames})";

            $status = 'sent';
            $errorMessage = null;

            try {
                Mail::to($targetEmail)->send(new KtamUpdateNotificationMail($owner, $cats, $customNote, $adminName));
                $sentCount++;
            } catch (\Throwable $e) {
                $status = 'failed';
                $errorMessage = $e->getMessage();
                $failedCount++;
                Log::error("CLI Broadcast KTA update gagal ke {$targetEmail}: " . $e->getMessage());
            }

            // Summary log for Outbox
            $bodyLog = "Pemberitahuan Penyesuaian KTAKuMu untuk anabul: {$catNames} (Nomor: {$catCodes}).\n\n";
            if (!empty($customNote)) {
                $bodyLog .= "Catatan Admin:\n{$customNote}\n\n";
            }
            $bodyLog .= "Tautan Portal: " . route('dashboard');

            EmailOutbox::logOutbox([
                'sender_id' => $adminId,
                'recipient_email' => $targetEmail,
                'recipient_name' => $owner->name,
                'subject' => $subject,
                'body' => $bodyLog,
                'mail_type' => 'ktam_broadcast',
                'mailer' => config('mail.default', 'smtp'),
                'status' => $status,
                'error_message' => $errorMessage,
            ]);

            $bar->advance();

            // Slight throttle to avoid aggressive SMTP rate limiting
            usleep(100000); // 100ms
        }

        $bar->finish();
        $this->newLine(2);

        $this->info("Pengiriman selesai!");
        $this->info("✅ Berhasil terkirim: {$sentCount}");
        if ($failedCount > 0) {
            $this->error("❌ Gagal: {$failedCount} (dapat dicek di menu Kotak Keluar Admin)");
        }

        return Command::SUCCESS;
    }
}
