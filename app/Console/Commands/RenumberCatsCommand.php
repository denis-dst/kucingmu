<?php

namespace App\Console\Commands;

use App\Models\Cat;
use App\Models\KtamCard;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class RenumberCatsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cats:renumber-from-61 {--start=61 : Starting ID for cats} {--dry-run : Simulate without updating database}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Renumber all cat IDs to start from ID 61 (or custom start), and synchronize all related tables.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $startId = (int) $this->option('start');
        if ($startId < 1) {
            $startId = 61;
        }

        $dryRun = (bool) $this->option('dry-run');

        $this->info("==================================================");
        $this->info("  KucingMu - Renumber Cat IDs Script (Start: {$startId})");
        $this->info("==================================================");

        // Fetch all cats ordered by current id ASC
        $cats = DB::table('cats')->orderBy('id', 'asc')->get();

        if ($cats->isEmpty()) {
            $this->warn("Tabel cats kosong. Hanya mengatur AUTO_INCREMENT menjadi {$startId}.");
            if (!$dryRun) {
                DB::statement("ALTER TABLE cats AUTO_INCREMENT = {$startId}");
            }
            $this->info("Selesai! AUTO_INCREMENT tabel cats telah diatur ke {$startId}.");
            return Command::SUCCESS;
        }

        $totalCats = $cats->count();
        $this->info("Ditemukan {$totalCats} data kucing di database.");

        $mapping = [];
        $currentNewId = $startId;

        foreach ($cats as $cat) {
            $mapping[] = [
                'old_id' => $cat->id,
                'new_id' => $currentNewId,
                'name'   => $cat->name,
                'wilayah_code' => $cat->wilayah_code ?? '34',
                'old_unique_code' => $cat->unique_code,
                'new_unique_code' => ($cat->wilayah_code ?? '34') . '.kcg.' . str_pad($currentNewId, 4, '0', STR_PAD_LEFT),
            ];
            $currentNewId++;
        }

        // Display preview table
        $tableData = array_map(function ($item) {
            return [
                'Old ID' => $item['old_id'],
                'New ID' => $item['new_id'],
                'Nama'   => $item['name'],
                'Old Unique Code' => $item['old_unique_code'] ?? '-',
                'New Unique Code' => $item['new_unique_code'],
            ];
        }, $mapping);

        $this->table(['Old ID', 'New ID', 'Nama Kucing', 'Old Unique Code', 'New Unique Code'], $tableData);

        if ($dryRun) {
            $this->warn("[DRY-RUN] Tidak ada perubahan yang disimpan ke database.");
            return Command::SUCCESS;
        }

        if (!$this->confirm("Apakah Anda yakin ingin memperbarui ID semua kucing dan relasi tabelnya sekarang?", true)) {
            $this->warn("Proses dibatalkan.");
            return Command::FAILURE;
        }

        $this->info("Memproses update data...");

        DB::beginTransaction();

        try {
            // Disable foreign key checks to allow ID updates
            DB::statement('SET FOREIGN_KEY_CHECKS = 0');

            // 1. Temporary shift IDs to avoid unique collision if any new_id overlaps with old_id
            $tempOffset = 1000000;
            foreach ($mapping as $item) {
                $tempId = $item['old_id'] + $tempOffset;
                
                DB::table('cats')->where('id', $item['old_id'])->update(['id' => $tempId]);
                DB::table('appointments')->where('cat_id', $item['old_id'])->update(['cat_id' => $tempId]);
                DB::table('medical_records')->where('cat_id', $item['old_id'])->update(['cat_id' => $tempId]);
                DB::table('ktam_cards')->where('cat_id', $item['old_id'])->update(['cat_id' => $tempId]);
                DB::table('cat_photos')->where('cat_id', $item['old_id'])->update(['cat_id' => $tempId]);
            }

            // 2. Assign final new IDs and update related tables & codes
            foreach ($mapping as $item) {
                $tempId = $item['old_id'] + $tempOffset;
                $newId = $item['new_id'];

                // Update cats table
                DB::table('cats')->where('id', $tempId)->update([
                    'id' => $newId,
                    'unique_code' => $item['new_unique_code'],
                ]);

                // Update appointments
                DB::table('appointments')->where('cat_id', $tempId)->update([
                    'cat_id' => $newId,
                ]);

                // Update medical records
                DB::table('medical_records')->where('cat_id', $tempId)->update([
                    'cat_id' => $newId,
                ]);

                // Update ktam_cards (also sync ktam_number if it matched old unique_code)
                $ktamCard = DB::table('ktam_cards')->where('cat_id', $tempId)->first();
                if ($ktamCard) {
                    $updateData = ['cat_id' => $newId];
                    if ($item['old_unique_code'] && $ktamCard->ktam_number === $item['old_unique_code']) {
                        $updateData['ktam_number'] = $item['new_unique_code'];
                    }
                    DB::table('ktam_cards')->where('id', $ktamCard->id)->update($updateData);
                }

                // Update cat photos
                DB::table('cat_photos')->where('cat_id', $tempId)->update([
                    'cat_id' => $newId,
                ]);
            }

            // 3. Set auto increment on cats to max(id) + 1
            $maxId = (int) DB::table('cats')->max('id');
            $nextAutoIncrement = max($maxId + 1, $currentNewId);
            DB::statement("ALTER TABLE cats AUTO_INCREMENT = {$nextAutoIncrement}");

            // Re-enable foreign key checks
            DB::statement('SET FOREIGN_KEY_CHECKS = 1');

            DB::commit();

            $this->info("==================================================");
            $this->info(" Berhasil! {$totalCats} data kucing telah diperbarui.");
            $this->info(" ID kucing sekarang dimulai dari {$startId} s.d. " . ($currentNewId - 1));
            $this->info(" AUTO_INCREMENT berikutnya: {$nextAutoIncrement}");
            $this->info(" Tabel yang disinkronkan:");
            $this->line("   - cats (id, unique_code)");
            $this->line("   - appointments (cat_id)");
            $this->line("   - medical_records (cat_id)");
            $this->line("   - ktam_cards (cat_id, ktam_number)");
            $this->line("   - cat_photos (cat_id)");
            $this->info("==================================================");

            return Command::SUCCESS;
        } catch (\Throwable $e) {
            DB::rollBack();
            DB::statement('SET FOREIGN_KEY_CHECKS = 1');
            $this->error("Terjadi kesalahan: " . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
