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
        Schema::table('cats', function (Blueprint $table) {
            if (!Schema::hasColumn('cats', 'biometric_type')) {
                $table->enum('biometric_type', ['none', 'paw', 'nose', 'both'])->default('none')->after('notes');
            }
            if (!Schema::hasColumn('cats', 'biometric_photo_path')) {
                $table->string('biometric_photo_path')->nullable()->after('biometric_type');
            }
            if (!Schema::hasColumn('cats', 'biometric_code')) {
                $table->string('biometric_code')->nullable()->after('biometric_photo_path');
            }
        });

        Schema::table('ktam_cards', function (Blueprint $table) {
            if (!Schema::hasColumn('ktam_cards', 'verified_by')) {
                $table->foreignId('verified_by')->nullable()->after('is_printed')->constrained('users')->onDelete('set null');
            }
            if (!Schema::hasColumn('ktam_cards', 'verified_at')) {
                $table->timestamp('verified_at')->nullable()->after('verified_by');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cats', function (Blueprint $table) {
            $columnsToDrop = [];
            if (Schema::hasColumn('cats', 'biometric_type')) $columnsToDrop[] = 'biometric_type';
            if (Schema::hasColumn('cats', 'biometric_photo_path')) $columnsToDrop[] = 'biometric_photo_path';
            if (Schema::hasColumn('cats', 'biometric_code')) $columnsToDrop[] = 'biometric_code';
            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });

        Schema::table('ktam_cards', function (Blueprint $table) {
            if (Schema::hasColumn('ktam_cards', 'verified_by')) {
                $table->dropForeign(['verified_by']);
                $table->dropColumn('verified_by');
            }
            if (Schema::hasColumn('ktam_cards', 'verified_at')) {
                $table->dropColumn('verified_at');
            }
        });
    }
};
