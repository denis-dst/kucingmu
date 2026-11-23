<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Helper to safely add an index only if it does not already exist.
     */
    private function addIndexSafely(string $tableName, array $columns, string $indexName): void
    {
        if (!Schema::hasTable($tableName)) {
            return;
        }

        try {
            Schema::table($tableName, function (Blueprint $table) use ($columns, $indexName) {
                $table->index($columns, $indexName);
            });
        } catch (\Throwable $e) {
            // Ignore if already exists
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
            // Ignore if not exists
        }
    }

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. prescriptions performance indexes for quick pharmacy queue & daily focus
        $this->addIndexSafely('prescriptions', ['status', 'issued_at'], 'prescriptions_status_issued_at_idx');
        $this->addIndexSafely('prescriptions', ['status', 'dispensed_at'], 'prescriptions_status_dispensed_at_idx');
        $this->addIndexSafely('prescriptions', ['status'], 'prescriptions_status_idx');
        $this->addIndexSafely('prescriptions', ['issued_at'], 'prescriptions_issued_at_idx');
        $this->addIndexSafely('prescriptions', ['dispensed_at'], 'prescriptions_dispensed_at_idx');

        // 2. prescription_items item-level dispense status index
        $this->addIndexSafely('prescription_items', ['prescription_id', 'is_dispensed'], 'prescription_items_rx_dispensed_idx');
        $this->addIndexSafely('prescription_items', ['is_dispensed'], 'prescription_items_is_dispensed_idx');

        // 3. medical_records status and examination date indexes
        $this->addIndexSafely('medical_records', ['status'], 'medical_records_status_idx');
        $this->addIndexSafely('medical_records', ['vet_id', 'status'], 'medical_records_vet_status_idx');
        $this->addIndexSafely('medical_records', ['service_type'], 'medical_records_service_type_idx');

        // 4. appointments quick queue lookups
        $this->addIndexSafely('appointments', ['date', 'status'], 'appointments_date_status_idx');
        $this->addIndexSafely('appointments', ['cat_id', 'date'], 'appointments_cat_date_idx');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $this->dropIndexSafely('appointments', 'appointments_cat_date_idx');
        $this->dropIndexSafely('appointments', 'appointments_date_status_idx');
        $this->dropIndexSafely('medical_records', 'medical_records_service_type_idx');
        $this->dropIndexSafely('medical_records', 'medical_records_vet_status_idx');
        $this->dropIndexSafely('medical_records', 'medical_records_status_idx');
        $this->dropIndexSafely('prescription_items', 'prescription_items_is_dispensed_idx');
        $this->dropIndexSafely('prescription_items', 'prescription_items_rx_dispensed_idx');
        $this->dropIndexSafely('prescriptions', 'prescriptions_dispensed_at_idx');
        $this->dropIndexSafely('prescriptions', 'prescriptions_issued_at_idx');
        $this->dropIndexSafely('prescriptions', 'prescriptions_status_idx');
        $this->dropIndexSafely('prescriptions', 'prescriptions_status_dispensed_at_idx');
        $this->dropIndexSafely('prescriptions', 'prescriptions_status_issued_at_idx');
    }
};
