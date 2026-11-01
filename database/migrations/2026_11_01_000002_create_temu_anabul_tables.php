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
        Schema::create('temu_anabuls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('cat_id')->nullable()->constrained('cats')->nullOnDelete();
            
            // Kategori Laporan: Hilang vs Ditemukan
            $table->enum('type', ['lost', 'found'])->default('lost');
            
            // Status Laporan
            $table->enum('status', ['active', 'reunited', 'closed'])->default('active');
            
            $table->string('title');
            $table->string('cat_name');
            $table->enum('gender', ['jantan', 'betina', 'tidak_diketahui'])->default('tidak_diketahui');
            $table->string('age_estimate', 100)->nullable();
            $table->string('coat_pattern', 100)->nullable();
            $table->string('coat_color', 100)->nullable();
            $table->text('distinctive_features')->comment('Ciri fisik khas: kalung, bentuk ekor, bekas luka');
            
            // Kondisi saat terlihat / ditemukan
            $table->enum('condition', ['sehat', 'terawat', 'sakit', 'terluka', 'lemas_kelaparan', 'tidak_diketahui'])->default('sehat');
            
            // Waktu Kejadian
            $table->dateTime('incident_date');
            
            // Lokasi Kejadian (GIS)
            $table->decimal('latitude', 10, 8);
            $table->decimal('longitude', 11, 8);
            $table->float('search_radius_km')->default(3.0);
            $table->string('province', 100)->nullable();
            $table->string('city', 100)->nullable();
            $table->string('district', 100)->nullable();
            $table->string('village', 100)->nullable();
            $table->text('formatted_address')->nullable();
            
            // Kontak Pelapor
            $table->string('contact_name');
            $table->string('contact_phone', 30);
            $table->boolean('is_contact_public')->default(true);
            
            // Info Tambahan
            $table->string('reward_info')->nullable()->comment('Informasi imbalan jika ada');
            $table->enum('holding_status', [
                'kept_by_finder',      // Diamankan sementara oleh penemu di rumah
                'at_clinic_shelter',   // Berada di klinik atau shelter terdekat
                'left_at_location',    // Masih berkeliaran di lokasi penemuan
                'unknown'
            ])->nullable()->comment('Status pengamanan untuk laporan ditemukan');
            $table->text('notes')->nullable();
            
            // Resolusi Temuan (Reunion)
            $table->dateTime('reunited_at')->nullable();
            $table->text('reunion_notes')->nullable();
            $table->string('reunion_photo_path')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['type', 'status'], 'temu_anabuls_type_status_idx');
            $table->index(['latitude', 'longitude'], 'temu_anabuls_spatial_idx');
            $table->index(['province', 'city'], 'temu_anabuls_wilayah_idx');
        });

        Schema::create('temu_anabul_photos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('temu_anabul_id')->constrained('temu_anabuls')->cascadeOnDelete();
            $table->string('photo_path');
            $table->boolean('is_primary')->default(false);
            $table->timestamps();

            $table->index(['temu_anabul_id', 'is_primary']);
        });

        Schema::create('temu_anabul_sightings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('temu_anabul_id')->constrained('temu_anabuls')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            
            $table->text('notes')->comment('Keterangan penampakan atau jejak anabul');
            $table->dateTime('sighting_time')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->string('location_name')->nullable();
            $table->string('photo_path')->nullable();
            $table->string('contact_phone', 30)->nullable();
            
            $table->timestamps();

            $table->index(['temu_anabul_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('temu_anabul_sightings');
        Schema::dropIfExists('temu_anabul_photos');
        Schema::dropIfExists('temu_anabuls');
    }
};

