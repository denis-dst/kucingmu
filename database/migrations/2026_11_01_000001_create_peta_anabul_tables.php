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
        Schema::create('peta_anabuls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('cat_id')->nullable()->constrained('cats')->nullOnDelete();
            
            // Peliharaan Member vs Kucing Liar (Pembeda Warna Pin di Peta)
            $table->enum('ownership_type', ['pet', 'stray'])->default('stray');
            
            $table->string('name')->comment('Nama panggilan atau kode identifikasi');
            $table->string('unique_code', 50)->unique();
            $table->string('coat_pattern', 100)->nullable()->comment('Pola bulu (Tabby, Tuxedo, Calico, Polos, dll)');
            $table->string('coat_color', 100)->nullable()->comment('Warna bulu dominan');
            $table->enum('gender', ['jantan', 'betina', 'tidak_diketahui'])->default('tidak_diketahui');
            $table->string('age_estimate', 100)->nullable()->comment('Perkiraan usia');
            $table->text('characteristics')->nullable()->comment('Ciri fisik khusus: mata, bentuk ekor, pola wajah, tanda khusus');
            
            // Kondisi Kucing
            $table->enum('condition', ['sehat', 'sakit', 'terluka', 'butuh_pertolongan', 'tidak_diketahui'])->default('sehat');
            $table->enum('sterilization_status', ['sudah', 'belum', 'tidak_diketahui'])->default('tidak_diketahui');
            
            // Status Keberadaan / Penanganan
            $table->enum('status', ['aktif', 'sudah_ditangani', 'tidak_terlihat_lagi', 'arsip'])->default('aktif');
            
            // Lokasi Spasial (GIS)
            $table->decimal('latitude', 10, 8);
            $table->decimal('longitude', 11, 8);
            $table->string('province', 100)->nullable();
            $table->string('city', 100)->nullable();
            $table->string('district', 100)->nullable();
            $table->string('village', 100)->nullable();
            $table->text('formatted_address')->nullable();
            
            $table->dateTime('last_seen_at')->nullable()->comment('Waktu terakhir terlihat');
            $table->text('description')->nullable()->comment('Deskripsi dan catatan tambahan');
            
            // Moderasi data komunitas
            $table->boolean('is_verified')->default(true);
            $table->enum('moderation_status', ['approved', 'pending_review', 'rejected'])->default('approved');
            
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['latitude', 'longitude'], 'peta_anabuls_spatial_idx');
            $table->index(['ownership_type', 'condition', 'status'], 'peta_anabuls_filter_idx');
            $table->index(['province', 'city'], 'peta_anabuls_wilayah_idx');
        });

        Schema::create('peta_anabul_photos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('peta_anabul_id')->constrained('peta_anabuls')->cascadeOnDelete();
            $table->string('photo_path');
            $table->string('caption')->nullable();
            $table->boolean('is_primary')->default(false);
            $table->timestamps();

            $table->index(['peta_anabul_id', 'is_primary']);
        });

        Schema::create('peta_anabul_updates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('peta_anabul_id')->constrained('peta_anabuls')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            
            // Jenis Kontribusi Komunitas
            $table->enum('update_type', [
                'sighting',             // Informasi keberadaan terbaru
                'condition_update',     // Pembaruan kondisi (misal: sembuh, sakit)
                'handled',              // Sudah ditangani (misal: dibawa ke klinik/shelter)
                'not_seen',             // Sudah tidak terlihat lagi di lokasi
                'correction',           // Koreksi ciri fisik atau lokasi
                'duplicate_report',     // Laporan data duplikat
                'report_inappropriate'  // Lapor konten tidak sesuai
            ])->default('sighting');
            
            $table->text('notes');
            $table->string('condition')->nullable();
            $table->string('status')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->string('photo_path')->nullable();
            
            // Moderasi Riwayat
            $table->enum('moderation_status', ['approved', 'pending', 'rejected'])->default('approved');
            $table->text('moderation_notes')->nullable();
            $table->foreignId('moderated_by')->nullable()->constrained('users')->nullOnDelete();
            
            $table->timestamps();

            $table->index(['peta_anabul_id', 'update_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('peta_anabul_updates');
        Schema::dropIfExists('peta_anabul_photos');
        Schema::dropIfExists('peta_anabuls');
    }
};

