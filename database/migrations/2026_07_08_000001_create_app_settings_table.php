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
        Schema::create('app_settings', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->text('value')->nullable();
            $table->string('label');
            $table->string('type')->default('text');
            $table->timestamps();
        });

        // Seed default values
        DB::table('app_settings')->insert([
            [
                'key' => 'app_name',
                'value' => 'KucingMu',
                'label' => 'Nama Aplikasi',
                'type' => 'text',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'contact_email',
                'value' => 'no-reply@kucingmu.online',
                'label' => 'Email Kontak Resmi',
                'type' => 'text',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'allow_registrations',
                'value' => '1',
                'label' => 'Izinkan Pendaftaran Kucing Baru (1 = Ya, 0 = Tidak)',
                'type' => 'boolean',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'maintenance_mode',
                'value' => '0',
                'label' => 'Mode Pemeliharaan / Maintenance (1 = Aktif, 0 = Tidak)',
                'type' => 'boolean',
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
        Schema::dropIfExists('app_settings');
    }
};
