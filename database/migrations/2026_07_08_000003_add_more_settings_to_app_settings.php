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
        DB::table('app_settings')->insert([
            [
                'key' => 'app_description',
                'value' => 'Platform terpadu untuk pendaftaran kucing, pemeriksaan kesehatan, dan penerbitan KTAM KucingMu.',
                'label' => 'Deskripsi Aplikasi',
                'type' => 'textarea',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'app_footer',
                'value' => '&copy; 2026 KucingMu. Warga Muhammadiyah Peduli Hewan. Seluruh hak cipta dilindungi.',
                'label' => 'Teks Footer / Hak Cipta',
                'type' => 'text',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'app_logo',
                'value' => null,
                'label' => 'Logo Aplikasi (Upload Gambar)',
                'type' => 'file',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'app_favicon',
                'value' => null,
                'label' => 'Favicon Aplikasi (Upload .ico/.png)',
                'type' => 'file',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('app_settings')->whereIn('key', ['app_description', 'app_footer', 'app_logo', 'app_favicon'])->delete();
    }
};
