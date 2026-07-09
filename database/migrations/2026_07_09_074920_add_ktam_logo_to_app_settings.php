<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('app_settings')->insert([
            'key' => 'ktam_logo',
            'value' => null,
            'label' => 'Logo Kartu KTAM (Kosongi jika ingin pakai logo utama)',
            'type' => 'file',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('app_settings')->where('key', 'ktam_logo')->delete();
    }
};
