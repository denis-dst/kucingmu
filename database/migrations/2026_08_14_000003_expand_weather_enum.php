<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Alter the enum to include expanded weather options
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE stray_cat_surveys MODIFY COLUMN weather ENUM('cerah','berawan','hujan','hujan ringan','hujan lebat') NULL");
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE stray_cat_surveys MODIFY COLUMN weather ENUM('cerah','berawan','hujan') NULL");
        }
    }
};
