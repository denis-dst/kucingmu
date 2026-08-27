<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Helper to safely add an index only if it does not already exist.
     */
    private function addIndexSafely(string $tableName, array $columns, string $indexName): void
    {
        if (!Schema::hasTable($tableName)) {
            return;
        }

        try {
            $sm = Schema::getConnection()->getDoctrineSchemaManager();
            $doctrineTable = $sm->introspectTable($tableName);
            if (!$doctrineTable->hasIndex($indexName)) {
                Schema::table($tableName, function (Blueprint $table) use ($columns, $indexName) {
                    $table->index($columns, $indexName);
                });
            }
        } catch (\Throwable $e) {
            // Fallback try adding directly with catch
            try {
                Schema::table($tableName, function (Blueprint $table) use ($columns, $indexName) {
                    $table->index($columns, $indexName);
                });
            } catch (\Throwable $e2) {
                // Ignore if duplicate
            }
        }
    }

    /**
     * Helper to safely drop an index.
     */
    private function dropIndexSafely(string $tableName, string $indexName): void
    {
        if (!Schema::hasTable($tableName)) {
            return;
        }

        try {
            Schema::table($tableName, function (Blueprint $table) use ($indexName) {
                $table->dropIndex($indexName);
            });
        } catch (\Throwable $e) {
            // Ignore if index not found
        }
    }

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. users table indexes
        $this->addIndexSafely('users', ['role'], 'users_role_index');
        $this->addIndexSafely('users', ['phone'], 'users_phone_index');
        $this->addIndexSafely('users', ['muhammadiyah_id'], 'users_muhammadiyah_id_index');

        // 2. cats table indexes
        $this->addIndexSafely('cats', ['user_id', 'created_at'], 'cats_user_id_created_at_index');
        $this->addIndexSafely('cats', ['wilayah_code'], 'cats_wilayah_code_index');
        $this->addIndexSafely('cats', ['breed'], 'cats_breed_index');
        $this->addIndexSafely('cats', ['gender'], 'cats_gender_index');
        $this->addIndexSafely('cats', ['created_at'], 'cats_created_at_index');

        // 3. appointments table indexes (FK & scheduling queries)
        $this->addIndexSafely('appointments', ['cat_id', 'date'], 'appointments_cat_id_date_index');
        $this->addIndexSafely('appointments', ['date', 'status'], 'appointments_date_status_index');
        $this->addIndexSafely('appointments', ['status'], 'appointments_status_index');

        // 4. medical_records table indexes (FK & lookup queries)
        $this->addIndexSafely('medical_records', ['cat_id', 'created_at'], 'medical_records_cat_id_created_at_index');
        $this->addIndexSafely('medical_records', ['vet_id'], 'medical_records_vet_id_index');
        $this->addIndexSafely('medical_records', ['appointment_id'], 'medical_records_appointment_id_index');

        // 5. ktam_cards table indexes
        $this->addIndexSafely('ktam_cards', ['issue_date'], 'ktam_cards_issue_date_index');
        $this->addIndexSafely('ktam_cards', ['is_printed'], 'ktam_cards_is_printed_index');

        // 6. cat_photos table indexes
        $this->addIndexSafely('cat_photos', ['cat_id', 'is_primary'], 'cat_photos_cat_id_is_primary_index');

        // 7. ptma_cat_censuses table indexes
        $this->addIndexSafely('ptma_cat_censuses', ['volunteer_id'], 'ptma_cat_censuses_volunteer_id_index');
        $this->addIndexSafely('ptma_cat_censuses', ['kampus', 'sequence_number'], 'ptma_cat_censuses_kampus_seq_index');
        $this->addIndexSafely('ptma_cat_censuses', ['kampus', 'zona'], 'ptma_cat_censuses_kampus_zona_index');
        $this->addIndexSafely('ptma_cat_censuses', ['warna'], 'ptma_cat_censuses_warna_index');
        $this->addIndexSafely('ptma_cat_censuses', ['created_at'], 'ptma_cat_censuses_created_at_index');

        // 8. stray_cat_surveys table indexes
        $this->addIndexSafely('stray_cat_surveys', ['volunteer_id'], 'stray_cat_surveys_volunteer_id_index');
        $this->addIndexSafely('stray_cat_surveys', ['survey_date'], 'stray_cat_surveys_survey_date_index');
        $this->addIndexSafely('stray_cat_surveys', ['created_at'], 'stray_cat_surveys_created_at_index');

        // 9. events table indexes
        $this->addIndexSafely('events', ['status', 'date'], 'events_status_date_index');

        // 10. activity_albums table indexes
        $this->addIndexSafely('activity_albums', ['is_active', 'order', 'activity_date'], 'activity_albums_active_order_date_index');

        // 11. master_wilayahs table indexes
        $this->addIndexSafely('master_wilayahs', ['is_active', 'order'], 'master_wilayahs_is_active_order_index');

        // 12. master_breeds table indexes
        $this->addIndexSafely('master_breeds', ['is_default', 'order'], 'master_breeds_is_default_order_index');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $this->dropIndexSafely('users', 'users_role_index');
        $this->dropIndexSafely('users', 'users_phone_index');
        $this->dropIndexSafely('users', 'users_muhammadiyah_id_index');

        $this->dropIndexSafely('cats', 'cats_user_id_created_at_index');
        $this->dropIndexSafely('cats', 'cats_wilayah_code_index');
        $this->dropIndexSafely('cats', 'cats_breed_index');
        $this->dropIndexSafely('cats', 'cats_gender_index');
        $this->dropIndexSafely('cats', 'cats_created_at_index');

        $this->dropIndexSafely('appointments', 'appointments_cat_id_date_index');
        $this->dropIndexSafely('appointments', 'appointments_date_status_index');
        $this->dropIndexSafely('appointments', 'appointments_status_index');

        $this->dropIndexSafely('medical_records', 'medical_records_cat_id_created_at_index');
        $this->dropIndexSafely('medical_records', 'medical_records_vet_id_index');
        $this->dropIndexSafely('medical_records', 'medical_records_appointment_id_index');

        $this->dropIndexSafely('ktam_cards', 'ktam_cards_issue_date_index');
        $this->dropIndexSafely('ktam_cards', 'ktam_cards_is_printed_index');

        $this->dropIndexSafely('cat_photos', 'cat_photos_cat_id_is_primary_index');

        $this->dropIndexSafely('ptma_cat_censuses', 'ptma_cat_censuses_volunteer_id_index');
        $this->dropIndexSafely('ptma_cat_censuses', 'ptma_cat_censuses_kampus_seq_index');
        $this->dropIndexSafely('ptma_cat_censuses', 'ptma_cat_censuses_kampus_zona_index');
        $this->dropIndexSafely('ptma_cat_censuses', 'ptma_cat_censuses_warna_index');
        $this->dropIndexSafely('ptma_cat_censuses', 'ptma_cat_censuses_created_at_index');

        $this->dropIndexSafely('stray_cat_surveys', 'stray_cat_surveys_volunteer_id_index');
        $this->dropIndexSafely('stray_cat_surveys', 'stray_cat_surveys_survey_date_index');
        $this->dropIndexSafely('stray_cat_surveys', 'stray_cat_surveys_created_at_index');

        $this->dropIndexSafely('events', 'events_status_date_index');
        $this->dropIndexSafely('activity_albums', 'activity_albums_active_order_date_index');
        $this->dropIndexSafely('master_wilayahs', 'master_wilayahs_is_active_order_index');
        $this->dropIndexSafely('master_breeds', 'master_breeds_is_default_order_index');
    }
};
