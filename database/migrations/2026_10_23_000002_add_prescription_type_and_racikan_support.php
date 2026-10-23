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
        Schema::table('prescriptions', function (Blueprint $table) {
            if (!Schema::hasColumn('prescriptions', 'prescription_type')) {
                $table->string('prescription_type', 30)->default('regular')->after('prescribed_by');
            }
            if (!Schema::hasColumn('prescriptions', 'notes')) {
                $table->text('notes')->nullable()->after('prescription_type');
            }
        });

        Schema::table('prescription_items', function (Blueprint $table) {
            if (!Schema::hasColumn('prescription_items', 'is_racikan')) {
                $table->boolean('is_racikan')->default(false)->after('medicine_name_snapshot');
            }
            if (!Schema::hasColumn('prescription_items', 'racikan_name')) {
                $table->string('racikan_name')->nullable()->after('is_racikan');
            }
            if (!Schema::hasColumn('prescription_items', 'composition')) {
                $table->text('composition')->nullable()->after('racikan_name');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('prescription_items', function (Blueprint $table) {
            if (Schema::hasColumn('prescription_items', 'composition')) {
                $table->dropColumn('composition');
            }
            if (Schema::hasColumn('prescription_items', 'racikan_name')) {
                $table->dropColumn('racikan_name');
            }
            if (Schema::hasColumn('prescription_items', 'is_racikan')) {
                $table->dropColumn('is_racikan');
            }
        });

        Schema::table('prescriptions', function (Blueprint $table) {
            if (Schema::hasColumn('prescriptions', 'notes')) {
                $table->dropColumn('notes');
            }
            if (Schema::hasColumn('prescriptions', 'prescription_type')) {
                $table->dropColumn('prescription_type');
            }
        });
    }
};
