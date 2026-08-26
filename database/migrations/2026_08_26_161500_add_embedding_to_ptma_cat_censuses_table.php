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
        Schema::table('ptma_cat_censuses', function (Blueprint $table) {
            if (!Schema::hasColumn('ptma_cat_censuses', 'foto_wajah_embedding')) {
                $table->longText('foto_wajah_embedding')->nullable()->after('foto_opsional');
            }
            if (!Schema::hasColumn('ptma_cat_censuses', 'color_fingerprint')) {
                $table->text('color_fingerprint')->nullable()->after('foto_wajah_embedding');
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
            if (Schema::hasColumn('ptma_cat_censuses', 'foto_wajah_embedding')) {
                $cols[] = 'foto_wajah_embedding';
            }
            if (Schema::hasColumn('ptma_cat_censuses', 'color_fingerprint')) {
                $cols[] = 'color_fingerprint';
            }
            if (!empty($cols)) {
                $table->dropColumn($cols);
            }
        });
    }
};
