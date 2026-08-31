<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use App\Models\Cat;
use App\Models\KtamCard;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (DB::getDriverName() === 'mysql') {
            // 1. Update cats.unique_code based on wilayah_code and id
            // Format: {wilayah_code}.kcg.{4-digit ID}
            // If wilayah_code is NULL/empty -> set unique_code to NULL
            DB::statement("
                UPDATE cats 
                SET unique_code = CASE 
                    WHEN wilayah_code IS NOT NULL AND TRIM(wilayah_code) != '' 
                        THEN CONCAT(LOWER(TRIM(wilayah_code)), '.kcg.', LPAD(id, 4, '0'))
                    ELSE NULL 
                END
            ");

            // 2. Synchronize existing ktam_cards.ktam_number with cats.unique_code
            DB::statement("
                UPDATE ktam_cards k
                JOIN cats c ON k.cat_id = c.id
                SET k.ktam_number = c.unique_code
                WHERE c.unique_code IS NOT NULL AND c.unique_code != ''
            ");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No reverse necessary as this is a data synchronization migration
    }
};
