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
        // 1. ptma_cat_censuses
        Schema::table('ptma_cat_censuses', function (Blueprint $table) {
            if (!Schema::hasColumn('ptma_cat_censuses', 'multi_embeddings')) {
                $table->longText('multi_embeddings')->nullable()->after('foto_wajah_embedding');
            }
            if (!Schema::hasColumn('ptma_cat_censuses', 'spatial_fingerprint')) {
                $table->longText('spatial_fingerprint')->nullable()->after('color_fingerprint');
            }
        });

        // 2. cats
        Schema::table('cats', function (Blueprint $table) {
            if (!Schema::hasColumn('cats', 'photo_embedding')) {
                $table->longText('photo_embedding')->nullable()->after('biometric_code');
            }
            if (!Schema::hasColumn('cats', 'color_fingerprint')) {
                $table->text('color_fingerprint')->nullable()->after('photo_embedding');
            }
            if (!Schema::hasColumn('cats', 'spatial_fingerprint')) {
                $table->longText('spatial_fingerprint')->nullable()->after('color_fingerprint');
            }
        });

        // 3. cat_photos
        Schema::table('cat_photos', function (Blueprint $table) {
            if (!Schema::hasColumn('cat_photos', 'photo_embedding')) {
                $table->longText('photo_embedding')->nullable()->after('is_primary');
            }
            if (!Schema::hasColumn('cat_photos', 'color_fingerprint')) {
                $table->text('color_fingerprint')->nullable()->after('photo_embedding');
            }
            if (!Schema::hasColumn('cat_photos', 'spatial_fingerprint')) {
                $table->longText('spatial_fingerprint')->nullable()->after('color_fingerprint');
            }
        });

        // 4. stray_cat_surveys
        Schema::table('stray_cat_surveys', function (Blueprint $table) {
            if (!Schema::hasColumn('stray_cat_surveys', 'photo_embedding')) {
                $table->longText('photo_embedding')->nullable()->after('photo_path');
            }
            if (!Schema::hasColumn('stray_cat_surveys', 'color_fingerprint')) {
                $table->text('color_fingerprint')->nullable()->after('photo_embedding');
            }
            if (!Schema::hasColumn('stray_cat_surveys', 'spatial_fingerprint')) {
                $table->longText('spatial_fingerprint')->nullable()->after('color_fingerprint');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ptma_cat_censuses', function (Blueprint $table) {
            $cols = [];
            if (Schema::hasColumn('ptma_cat_censuses', 'multi_embeddings')) $cols[] = 'multi_embeddings';
            if (Schema::hasColumn('ptma_cat_censuses', 'spatial_fingerprint')) $cols[] = 'spatial_fingerprint';
            if (!empty($cols)) $table->dropColumn($cols);
        });

        Schema::table('cats', function (Blueprint $table) {
            $cols = [];
            if (Schema::hasColumn('cats', 'photo_embedding')) $cols[] = 'photo_embedding';
            if (Schema::hasColumn('cats', 'color_fingerprint')) $cols[] = 'color_fingerprint';
            if (Schema::hasColumn('cats', 'spatial_fingerprint')) $cols[] = 'spatial_fingerprint';
            if (!empty($cols)) $table->dropColumn($cols);
        });

        Schema::table('cat_photos', function (Blueprint $table) {
            $cols = [];
            if (Schema::hasColumn('cat_photos', 'photo_embedding')) $cols[] = 'photo_embedding';
            if (Schema::hasColumn('cat_photos', 'color_fingerprint')) $cols[] = 'color_fingerprint';
            if (Schema::hasColumn('cat_photos', 'spatial_fingerprint')) $cols[] = 'spatial_fingerprint';
            if (!empty($cols)) $table->dropColumn($cols);
        });

        Schema::table('stray_cat_surveys', function (Blueprint $table) {
            $cols = [];
            if (Schema::hasColumn('stray_cat_surveys', 'photo_embedding')) $cols[] = 'photo_embedding';
            if (Schema::hasColumn('stray_cat_surveys', 'color_fingerprint')) $cols[] = 'color_fingerprint';
            if (Schema::hasColumn('stray_cat_surveys', 'spatial_fingerprint')) $cols[] = 'spatial_fingerprint';
            if (!empty($cols)) $table->dropColumn($cols);
        });
    }
};
