<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Add pharmacy dispense tracking fields to prescriptions table
        if (Schema::hasTable('prescriptions')) {
            Schema::table('prescriptions', function (Blueprint $table) {
                if (!Schema::hasColumn('prescriptions', 'dispensed_at')) {
                    $table->timestamp('dispensed_at')->nullable()->after('issued_at');
                }
                if (!Schema::hasColumn('prescriptions', 'dispensed_by')) {
                    $table->foreignId('dispensed_by')->nullable()->after('dispensed_at')->constrained('users')->nullOnDelete();
                }
                if (!Schema::hasColumn('prescriptions', 'dispense_notes')) {
                    $table->text('dispense_notes')->nullable()->after('dispensed_by');
                }
            });
        }

        // 2. Add item-level dispense status to prescription_items table
        if (Schema::hasTable('prescription_items')) {
            Schema::table('prescription_items', function (Blueprint $table) {
                if (!Schema::hasColumn('prescription_items', 'is_dispensed')) {
                    $table->boolean('is_dispensed')->default(false)->after('warnings');
                }
                if (!Schema::hasColumn('prescription_items', 'dispensed_at')) {
                    $table->timestamp('dispensed_at')->nullable()->after('is_dispensed');
                }
            });
        }

        // 3. Create cat_condolences table for deceased cats
        if (!Schema::hasTable('cat_condolences')) {
            Schema::create('cat_condolences', function (Blueprint $table) {
                $table->id();
                $table->foreignId('cat_id')->constrained('cats')->cascadeOnDelete();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->text('message');
                $table->string('prayer_text', 500)->nullable();
                $table->boolean('is_visible')->default(true);
                $table->timestamps();

                $table->index(['cat_id', 'created_at']);
            });
        }

        // 4. Create user_notifications table
        if (!Schema::hasTable('user_notifications')) {
            Schema::create('user_notifications', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->foreignId('sender_id')->nullable()->constrained('users')->nullOnDelete();
                $table->string('type', 50)->default('general');
                $table->string('title', 255);
                $table->text('message');
                $table->string('url', 500)->nullable();
                $table->string('icon', 50)->nullable()->default('bell');
                $table->json('data')->nullable();
                $table->timestamp('read_at')->nullable();
                $table->timestamps();

                $table->index(['user_id', 'read_at']);
                $table->index(['user_id', 'created_at']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_notifications');
        Schema::dropIfExists('cat_condolences');

        if (Schema::hasTable('prescription_items')) {
            Schema::table('prescription_items', function (Blueprint $table) {
                if (Schema::hasColumn('prescription_items', 'dispensed_at')) {
                    $table->dropColumn('dispensed_at');
                }
                if (Schema::hasColumn('prescription_items', 'is_dispensed')) {
                    $table->dropColumn('is_dispensed');
                }
            });
        }

        if (Schema::hasTable('prescriptions')) {
            Schema::table('prescriptions', function (Blueprint $table) {
                if (Schema::hasColumn('prescriptions', 'dispense_notes')) {
                    $table->dropColumn('dispense_notes');
                }
                if (Schema::hasColumn('prescriptions', 'dispensed_by')) {
                    $table->dropForeign(['dispensed_by']);
                    $table->dropColumn('dispensed_by');
                }
                if (Schema::hasColumn('prescriptions', 'dispensed_at')) {
                    $table->dropColumn('dispensed_at');
                }
            });
        }
    }
};
