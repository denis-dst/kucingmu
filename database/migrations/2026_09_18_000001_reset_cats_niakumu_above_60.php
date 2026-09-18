<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Reset unique_code to NULL for cats with ID >= 61
        DB::table('cats')
            ->where('id', '>=', 61)
            ->update(['unique_code' => null]);

        // 2. Also reset unique_code for cats whose unique_code suffix number is >= 61 (e.g. *.kcg.0061+)
        DB::statement("
            UPDATE cats 
            SET unique_code = NULL 
            WHERE unique_code IS NOT NULL 
              AND CAST(SUBSTRING_INDEX(unique_code, '.', -1) AS UNSIGNED) >= 61
        ");

        // 3. Remove KTAM cards for unverified cats (ID >= 61 or unique_code IS NULL)
        DB::table('ktam_cards')
            ->where('cat_id', '>=', 61)
            ->delete();

        DB::statement("
            DELETE FROM ktam_cards 
            WHERE cat_id IN (SELECT id FROM cats WHERE unique_code IS NULL)
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $cats = DB::table('cats')->whereNull('unique_code')->where('id', '>=', 61)->get();
        foreach ($cats as $cat) {
            $wilayah = $cat->wilayah_code ?: '34';
            $code = strtolower(trim($wilayah)) . '.kcg.' . str_pad($cat->id, 4, '0', STR_PAD_LEFT);
            DB::table('cats')->where('id', $cat->id)->update(['unique_code' => $code]);
        }
    }
};
