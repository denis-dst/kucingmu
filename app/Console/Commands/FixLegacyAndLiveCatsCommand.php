<?php

namespace App\Console\Commands;

use App\Models\Cat;
use App\Models\KtamCard;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class FixLegacyAndLiveCatsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cats:fix-legacy-and-live {--dry-run : Preview changes without executing}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Safely separate and renumber LIVE cats (IDs 61+) from LEGACY cats (IDs 1-60) and restore relations.';

    /**
     * Daftar 23 Kucing Legacy dari Spreadsheet KTA (1-60).
     */
    protected array $legacyData = [
        ['id' => 2,  'unique_code' => '12.kcg.0002', 'name' => 'Gree',           'breed' => 'Campuran Jawa BSH',          'gender' => 'male',   'color' => 'Abu campur putih di dada', 'dob' => '2025-02-14', 'owner_name' => 'Hertini',                       'owner_phone' => '085328982180', 'nbm' => '12.kcmg.02'],
        ['id' => 3,  'unique_code' => '12.kcg.0003', 'name' => 'Tom',            'breed' => 'Domestik',                   'gender' => 'male',   'color' => 'Putih orange',             'dob' => '2022-12-01', 'owner_name' => 'Endang',                        'owner_phone' => '08158985185',  'nbm' => null],
        ['id' => 5,  'unique_code' => '12.kcg.0005', 'name' => 'Kayya',          'breed' => 'Domestik (Mujair)',          'gender' => 'female', 'color' => 'Tabby',                    'dob' => '2026-04-01', 'owner_name' => 'Hasheena Jasmine Pamena',        'owner_phone' => '085932861148', 'nbm' => '1410164'],
        ['id' => 6,  'unique_code' => '12.kcg.0006', 'name' => 'BigShe',         'breed' => 'Lokal',                      'gender' => 'female', 'color' => 'Putih ekor Abu-abu',        'dob' => '2026-03-01', 'owner_name' => 'Nur Fianto',                     'owner_phone' => '081328604924', 'nbm' => null],
        ['id' => 7,  'unique_code' => '12.kcg.0007', 'name' => 'Benierka',       'breed' => 'Domestik',                   'gender' => 'female', 'color' => 'Oren Putih',                'dob' => '2025-06-03', 'owner_name' => 'Dyah Ngesti Rahayu Lestari',    'owner_phone' => '081804304699', 'nbm' => null],
        ['id' => 8,  'unique_code' => '12.kcg.0008', 'name' => 'Inul Ratnasari', 'breed' => 'Domestik',                   'gender' => 'female', 'color' => 'Oren Putih',                'dob' => '2025-10-23', 'owner_name' => 'Dyah Ngesti Rahayu Lestari',    'owner_phone' => '081804304699', 'nbm' => null],
        ['id' => 9,  'unique_code' => '12.kcg.0009', 'name' => 'Abu',            'breed' => 'Domestik',                   'gender' => 'male',   'color' => 'Abu-abu',                  'dob' => '2025-01-01', 'owner_name' => 'Daffa Fauzia Rohman',            'owner_phone' => '087829403895', 'nbm' => '1564922'],
        ['id' => 11, 'unique_code' => '12.kcg.0011', 'name' => 'Boyy',           'breed' => 'Domestik',                   'gender' => 'male',   'color' => 'Oren Putih',                'dob' => '2025-06-01', 'owner_name' => 'Laila Maulidina',                'owner_phone' => '089674241060', 'nbm' => null],
        ['id' => 14, 'unique_code' => '12.kcg.0014', 'name' => 'Brothuk',        'breed' => 'Domestik',                   'gender' => 'male',   'color' => 'Oren',                      'dob' => '2025-07-01', 'owner_name' => 'Muhammad Faiz Muzaffar',         'owner_phone' => '089518561951', 'nbm' => '120303221437480'],
        ['id' => 15, 'unique_code' => '12.kcg.0015', 'name' => 'Frangky',        'breed' => 'Mixdom',                     'gender' => 'male',   'color' => 'Hitam, Abu, Putih',         'dob' => '2023-01-06', 'owner_name' => 'Kaharani Annindya Nissa',        'owner_phone' => '085155328871', 'nbm' => null],
        ['id' => 23, 'unique_code' => '12.kcg.0023', 'name' => 'Ken',            'breed' => 'Persia Flatnose long hair',  'gender' => 'male',   'color' => 'Cream & Putih',             'dob' => '2025-07-01', 'owner_name' => 'Azizatur Rohmah',               'owner_phone' => '085843286636', 'nbm' => '12.KCG.0023'],
        ['id' => 25, 'unique_code' => '12.kcg.0025', 'name' => 'Ucil',           'breed' => 'Domestik',                   'gender' => 'male',   'color' => 'Putih Abu',                 'dob' => '2026-01-01', 'owner_name' => 'Nur Akhda Sabila',              'owner_phone' => '082314842823', 'nbm' => '1398279 / 120295211398279'],
        ['id' => 38, 'unique_code' => '12.kcg.0038', 'name' => 'Piu',            'breed' => 'Ras himalaya',               'gender' => 'female', 'color' => 'Putih',                     'dob' => '2025-12-01', 'owner_name' => 'Vira Melinda',                   'owner_phone' => '082137286876', 'nbm' => '12.KCG.0038'],
        ['id' => 40, 'unique_code' => '12.kcg.0040', 'name' => 'Budi',           'breed' => 'Mixdom',                     'gender' => 'male',   'color' => 'Hitam-putih',                'dob' => '2026-02-23', 'owner_name' => 'Azzahra Sajida Salmadevi',       'owner_phone' => '085865240893', 'nbm' => null],
        ['id' => 41, 'unique_code' => '12.kcg.0041', 'name' => 'Kumit',          'breed' => 'Mixdom',                     'gender' => 'female', 'color' => 'Putih',                     'dob' => '2026-02-23', 'owner_name' => 'Azzahra Sajida Salmadevi',       'owner_phone' => '085865240893', 'nbm' => null],
        ['id' => 42, 'unique_code' => '12.kcg.0042', 'name' => 'Devil',          'breed' => 'Mixdom',                     'gender' => 'male',   'color' => 'Abu2',                      'dob' => '2026-02-23', 'owner_name' => 'Azzahra Sajida Salmadevi',       'owner_phone' => '085865240893', 'nbm' => null],
        ['id' => 44, 'unique_code' => '12.kcg.0044', 'name' => 'Liona',          'breed' => 'Himalaya',                   'gender' => 'female', 'color' => 'Putih',                     'dob' => '2022-03-01', 'owner_name' => 'Ika Kusumayani',                 'owner_phone' => '089637195373', 'nbm' => '887520'],
        ['id' => 45, 'unique_code' => '12.kcg.0045', 'name' => 'White',          'breed' => 'Himalaya',                   'gender' => 'male',   'color' => 'Putih',                     'dob' => '2023-12-08', 'owner_name' => 'Nefiora Afia Fitzora',           'owner_phone' => '089605976761', 'nbm' => null],
        ['id' => 46, 'unique_code' => '12.kcg.0046', 'name' => 'Elio',           'breed' => 'Himalaya',                   'gender' => 'male',   'color' => 'Putih',                     'dob' => '2022-03-01', 'owner_name' => 'Shefinsa Afia Fitzora',          'owner_phone' => '089616074422', 'nbm' => null],
        ['id' => 47, 'unique_code' => '12.kcg.0047', 'name' => 'Neko',           'breed' => 'Mixdom',                     'gender' => 'female', 'color' => 'Putih corak oranye',         'dob' => '2024-07-01', 'owner_name' => 'Frida Zuhera Yustisiani Farid', 'owner_phone' => '081578711938', 'nbm' => '1100154'],
        ['id' => 48, 'unique_code' => '12.kcg.0048', 'name' => 'Leon',           'breed' => 'Domestik',                   'gender' => 'male',   'color' => 'Abu',                       'dob' => '2026-03-01', 'owner_name' => 'Ayu Rahmadhani',                 'owner_phone' => '089687519590', 'nbm' => null],
        ['id' => 59, 'unique_code' => '12.kcg.0059', 'name' => 'Skill',          'breed' => 'Mixdom',                     'gender' => 'female', 'color' => 'Putih',                     'dob' => '2026-02-23', 'owner_name' => 'Alfa Fitri Amalia Hilal',         'owner_phone' => '081932701243', 'nbm' => null],
        ['id' => 60, 'unique_code' => '12.kcg.0060', 'name' => 'Debu',           'breed' => 'Munchkin x Persia',          'gender' => 'male',   'color' => 'Abu2 putih',                 'dob' => '2025-05-01', 'owner_name' => 'Alfa Fitri Amalia Hilal',         'owner_phone' => '081932701243', 'nbm' => null],
    ];

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');

        $this->info("====================================================================");
        $this->info(" KucingMu - Recovery & Restrukturisasi Data (Live 61+ & Legacy 1-60)");
        $this->info("====================================================================");

        // 1. Ambil list unique_code legacy
        $legacyCodes = array_map(fn($item) => strtolower($item['unique_code']), $this->legacyData);
        $legacyPhoneEmails = array_map(function ($item) {
            $rawPhone = preg_replace('/[^0-9]/', '', $item['owner_phone']);
            return $rawPhone . '@kucingmu.online';
        }, $this->legacyData);

        // 2. Ambil semua kucing yang ada di database saat ini beserta ownernya
        $allCats = DB::table('cats')
            ->leftJoin('users', 'cats.user_id', '=', 'users.id')
            ->select('cats.*', 'users.email as user_email', 'users.name as user_name')
            ->orderBy('cats.id', 'asc')
            ->get();

        $liveCats = [];
        $existingLegacyCatIds = [];

        foreach ($allCats as $cat) {
            $catCode = strtolower(trim($cat->unique_code ?? ''));
            $userEmail = strtolower(trim($cat->user_email ?? ''));

            // Cek apakah kucing ini adalah salah satu dari 23 data legacy
            $isLegacy = in_array($catCode, $legacyCodes) || in_array($userEmail, $legacyPhoneEmails);

            if ($isLegacy) {
                $existingLegacyCatIds[] = $cat->id;
            } else {
                $liveCats[] = $cat;
            }
        }

        $this->info("Ditemukan " . count($allCats) . " total kucing saat ini di database.");
        $this->line(" - Data Kucing LIVE (Milik Pengguna Asli): " . count($liveCats));
        $this->line(" - Data Kucing LEGACY (Data 1-60): " . count($this->legacyData));

        // 3. Buat rencana renumbering untuk kucing LIVE agar mulai dari ID 61 ke atas
        $currentLiveTargetId = 61;
        $liveMapping = [];

        foreach ($liveCats as $cat) {
            $liveMapping[] = [
                'old_id' => $cat->id,
                'new_id' => $currentLiveTargetId,
                'name'   => $cat->name,
                'owner'  => $cat->user_name . ' (' . $cat->user_email . ')',
                'new_code' => ($cat->wilayah_code ?? '34') . '.kcg.' . str_pad($currentLiveTargetId, 4, '0', STR_PAD_LEFT),
            ];
            $currentLiveTargetId++;
        }

        if (!empty($liveMapping)) {
            $this->info("\nRencana Pergeseran Data Kucing LIVE (ke ID 61+):");
            $this->table(
                ['ID Lama', 'ID Baru', 'Nama Kucing', 'Pemilik', 'Unique Code Baru'],
                array_map(fn($m) => [$m['old_id'], $m['new_id'], $m['name'], $m['owner'], $m['new_code']], $liveMapping)
            );
        } else {
            $this->info("\nTidak ada data kucing live yang perlu digeser.");
        }

        if ($dryRun) {
            $this->warn("\n[DRY-RUN] Tidak ada perubahan yang disimpan ke database.");
            return Command::SUCCESS;
        }

        if (!$this->confirm("\nApakah Anda ingin menjalankan proses restrukturisasi & recovery sekarang?", true)) {
            $this->warn("Proses dibatalkan.");
            return Command::FAILURE;
        }

        $this->info("\nMemproses restrukturisasi database...");

        DB::beginTransaction();

        try {
            DB::statement('SET FOREIGN_KEY_CHECKS = 0');

            // LANGKAH 1: Geser semua kucing LIVE ke ID offset sementara (1000000+)
            $tempOffset = 1000000;
            foreach ($liveMapping as $m) {
                $tempId = $m['old_id'] + $tempOffset;
                DB::table('cats')->where('id', $m['old_id'])->update(['id' => $tempId]);
                DB::table('appointments')->where('cat_id', $m['old_id'])->update(['cat_id' => $tempId]);
                DB::table('medical_records')->where('cat_id', $m['old_id'])->update(['cat_id' => $tempId]);
                DB::table('ktam_cards')->where('cat_id', $m['old_id'])->update(['cat_id' => $tempId]);
                DB::table('cat_photos')->where('cat_id', $m['old_id'])->update(['cat_id' => $tempId]);
            }

            // LANGKAH 2: Bersihkan data legacy lama dari tabel cats agar slot 1-60 bersih & fresh
            foreach ($existingLegacyCatIds as $legId) {
                // Hanya hapus jika ID tersebut bukan tempId live
                DB::table('cats')->where('id', $legId)->delete();
                DB::table('ktam_cards')->where('cat_id', $legId)->delete();
            }

            // LANGKAH 3: Pindahkan kucing LIVE dari tempId ke ID 61, 62, 63...
            foreach ($liveMapping as $m) {
                $tempId = $m['old_id'] + $tempOffset;
                $newId = $m['new_id'];

                DB::table('cats')->where('id', $tempId)->update([
                    'id' => $newId,
                    'unique_code' => $m['new_code'],
                ]);
                DB::table('appointments')->where('cat_id', $tempId)->update(['cat_id' => $newId]);
                DB::table('medical_records')->where('cat_id', $tempId)->update(['cat_id' => $newId]);
                DB::table('ktam_cards')->where('cat_id', $tempId)->update([
                    'cat_id' => $newId,
                    'ktam_number' => $m['new_code'],
                ]);
                DB::table('cat_photos')->where('cat_id', $tempId)->update(['cat_id' => $newId]);
            }

            // LANGKAH 4: Masukkan kembali 23 Kucing LEGACY ke ID 1 - 60 yang sesuai
            $defaultPassword = Hash::make('password!1');

            foreach ($this->legacyData as $leg) {
                $rawPhone = preg_replace('/[^0-9]/', '', $leg['owner_phone']);
                $ownerEmail = $rawPhone . '@kucingmu.online';

                $owner = User::firstOrCreate(
                    ['email' => $ownerEmail],
                    [
                        'name'              => $leg['owner_name'],
                        'password'          => $defaultPassword,
                        'phone'             => $rawPhone,
                        'role'              => 'member',
                        'muhammadiyah_id'   => $leg['nbm'],
                        'email_verified_at' => Carbon::now(),
                    ]
                );

                $owner->update([
                    'name'            => $leg['owner_name'],
                    'phone'           => $rawPhone,
                    'muhammadiyah_id' => $leg['nbm'] ?: $owner->muhammadiyah_id,
                ]);

                // Insert / Update Cat Legacy di slot ID pastinya
                DB::table('cats')->updateOrInsert(
                    ['id' => $leg['id']],
                    [
                        'user_id'       => $owner->id,
                        'name'          => $leg['name'],
                        'breed'         => $leg['breed'],
                        'gender'        => $leg['gender'],
                        'date_of_birth' => $leg['dob'],
                        'wilayah_code'  => '12',
                        'unique_code'   => strtolower($leg['unique_code']),
                        'color'         => $leg['color'],
                        'notes'         => 'Data registrasi legacy pra-sistem.',
                        'created_at'    => Carbon::now(),
                        'updated_at'    => Carbon::now(),
                    ]
                );

                // Insert KTAM Card
                $verificationUrl = route('ktam.verify', ['number' => strtolower($leg['unique_code'])]);
                $qrCodeSvg = QrCode::size(200)
                    ->color(15, 118, 110)
                    ->backgroundColor(255, 255, 255)
                    ->generate($verificationUrl);

                DB::table('ktam_cards')->updateOrInsert(
                    ['cat_id' => $leg['id']],
                    [
                        'ktam_number'     => strtolower($leg['unique_code']),
                        'issue_date'      => Carbon::now()->toDateString(),
                        'qr_code_payload' => 'data:image/svg+xml;base64,' . base64_encode($qrCodeSvg),
                        'is_printed'      => true,
                        'created_at'      => Carbon::now(),
                        'updated_at'      => Carbon::now(),
                    ]
                );
            }

            // LANGKAH 5: Atur AUTO_INCREMENT tabel cats agar mulai setelah ID live tertinggi
            $maxCatId = (int) DB::table('cats')->max('id');
            $nextAutoIncrement = max($maxCatId + 1, 61);
            DB::statement("ALTER TABLE cats AUTO_INCREMENT = {$nextAutoIncrement}");

            DB::statement('SET FOREIGN_KEY_CHECKS = 1');

            DB::commit();

            $this->info("====================================================================");
            $this->info(" BERHASIL! Restrukturisasi database telah selesai.");
            $this->info(" 1. Slot ID 1 - 60 sekarang terisi bersih oleh 23 data Kucing Legacy.");
            $this->info(" 2. Semua data Kucing Live (Milik Pengguna Asli) sekarang berada di ID 61 ke atas.");
            $this->info(" 3. Relasi janji temu, rekam medis, kartu KTAM, dan foto telah tersinkron.");
            $this->info(" 4. AUTO_INCREMENT berikutnya: {$nextAutoIncrement}");
            $this->info("====================================================================");

            return Command::SUCCESS;
        } catch (\Throwable $e) {
            DB::rollBack();
            DB::statement('SET FOREIGN_KEY_CHECKS = 1');
            $this->error("Terjadi error saat recovery: " . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
