<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('cats', function (Blueprint $table) {
            if (!Schema::hasColumn('cats', 'verified_by')) {
                $table->foreignId('verified_by')->nullable()->after('deleted_by')->constrained('users')->onDelete('set null');
            }
            if (!Schema::hasColumn('cats', 'verified_at')) {
                $table->timestamp('verified_at')->nullable()->after('verified_by');
            }
        });

        // Sync existing verified cats from ktam_cards table
        try {
            DB::statement("UPDATE cats c INNER JOIN ktam_cards k ON c.id = k.cat_id SET c.verified_at = k.verified_at, c.verified_by = k.verified_by WHERE k.verified_at IS NOT NULL");
        } catch (\Throwable $e) {
            // Ignore if tables are empty or error during initial test
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cats', function (Blueprint $table) {
            if (Schema::hasColumn('cats', 'verified_by')) {
                $table->dropForeign(['verified_by']);
                $table->dropColumn('verified_by');
            }
            if (Schema::hasColumn('cats', 'verified_at')) {
                $table->dropColumn('verified_at');
            }
        });
    }
};
