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
        // 1. Create master_wilayahs table
        if (!Schema::hasTable('master_wilayahs')) {
            Schema::create('master_wilayahs', function (Blueprint $table) {
                $table->id();
                $table->string('kode', 10)->unique();
                $table->string('nama');
                $table->string('singkatan', 20)->nullable();
                $table->integer('urutan')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        // 2. Add wilayah_code, unique_code, and color to cats table
        Schema::table('cats', function (Blueprint $table) {
            if (!Schema::hasColumn('cats', 'wilayah_code')) {
                $table->string('wilayah_code', 10)->default('34')->nullable()->after('breed');
            }
            if (!Schema::hasColumn('cats', 'unique_code')) {
                $table->string('unique_code', 50)->nullable()->unique()->after('wilayah_code');
            }
            if (!Schema::hasColumn('cats', 'color')) {
                $table->string('color', 100)->nullable()->after('unique_code');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cats', function (Blueprint $table) {
            $cols = [];
            if (Schema::hasColumn('cats', 'wilayah_code')) $cols[] = 'wilayah_code';
            if (Schema::hasColumn('cats', 'unique_code')) $cols[] = 'unique_code';
            if (Schema::hasColumn('cats', 'color')) $cols[] = 'color';
            if (!empty($cols)) {
                $table->dropColumn($cols);
            }
        });

        Schema::dropIfExists('master_wilayahs');
    }
};
