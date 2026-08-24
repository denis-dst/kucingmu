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
        Schema::create('ptma_cat_censuses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('volunteer_id')->nullable()->constrained('users')->nullOnDelete();
            
            // A. Informasi Umum & Lokasi
            $table->string('id_kucing')->unique()->index();
            $table->unsignedInteger('sequence_number')->default(1);
            $table->string('kampus');
            $table->string('kampus_custom')->nullable();
            $table->string('zona');
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            
            // B. Identifikasi Individu & Foto
            $table->string('usia');
            $table->string('gender');
            $table->string('warna');
            $table->string('warna_custom')->nullable();
            $table->string('foto_wajah')->nullable();
            $table->string('foto_atas')->nullable();
            $table->string('foto_samping_kiri')->nullable();
            $table->string('foto_opsional')->nullable();
            
            // C. Kesejahteraan Fisik & Morfometri
            $table->string('bcs');
            $table->json('kondisi_klinis')->nullable();
            $table->decimal('panjang_badan_cm', 5, 1)->nullable();
            $table->decimal('panjang_ekor_cm', 5, 1)->nullable();
            
            // D. Kualitas Mikro-Habitat
            $table->integer('jarak_pakan')->nullable();
            $table->string('jenis_pakan');
            $table->string('jenis_pakan_custom')->nullable();
            $table->string('ancaman');
            $table->string('ancaman_custom')->nullable();
            $table->text('catatan')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ptma_cat_censuses');
    }
};
