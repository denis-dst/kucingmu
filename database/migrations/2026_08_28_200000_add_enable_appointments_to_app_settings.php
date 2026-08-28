<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!DB::table('app_settings')->where('key', 'enable_appointments')->exists()) {
            DB::table('app_settings')->insert([
                'key' => 'enable_appointments',
                'value' => '1',
                'label' => 'Fitur Janji Temu Pemeriksaan (1 = Aktif / Tampil, 0 = Nonaktif / Sembunyi)',
                'type' => 'boolean',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('app_settings')->where('key', 'enable_appointments')->delete();
    }
};
