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
            if (!Schema::hasColumn('ptma_cat_censuses', 'breed')) {
                $table->string('breed')->default('Domestik')->after('gender')->index();
            }
            if (!Schema::hasColumn('ptma_cat_censuses', 'breed_custom')) {
                $table->string('breed_custom')->nullable()->after('breed');
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
            if (Schema::hasColumn('ptma_cat_censuses', 'breed')) $cols[] = 'breed';
            if (Schema::hasColumn('ptma_cat_censuses', 'breed_custom')) $cols[] = 'breed_custom';
            if (!empty($cols)) $table->dropColumn($cols);
        });
    }
};
