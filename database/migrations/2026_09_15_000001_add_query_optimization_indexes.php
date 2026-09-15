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
            try {
                Schema::table($tableName, function (Blueprint $table) use ($columns, $indexName) {
                    $table->index($columns, $indexName);
                });
            } catch (\Throwable $e2) {
                // Ignore if already exists
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
        // 1. cats table indexes for status filtering, name searches, and date sorting
        $this->addIndexSafely('cats', ['status'], 'cats_status_index');
        $this->addIndexSafely('cats', ['name'], 'cats_name_index');
        $this->addIndexSafely('cats', ['date_of_birth'], 'cats_date_of_birth_index');

        // 2. social_posts table index for feed active status filtering
        $this->addIndexSafely('social_posts', ['is_active', 'created_at'], 'social_posts_is_active_created_at_index');

        // 3. stories table index for active story expiration lookup
        $this->addIndexSafely('stories', ['expires_at'], 'stories_expires_at_index');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $this->dropIndexSafely('cats', 'cats_status_index');
        $this->dropIndexSafely('cats', 'cats_name_index');
        $this->dropIndexSafely('cats', 'cats_date_of_birth_index');

        $this->dropIndexSafely('social_posts', 'social_posts_is_active_created_at_index');
        $this->dropIndexSafely('stories', 'stories_expires_at_index');
    }
};
