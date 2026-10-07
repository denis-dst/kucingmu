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
        // 1. Add adoption columns to cats table
        Schema::table('cats', function (Blueprint $table) {
            if (!Schema::hasColumn('cats', 'is_for_adoption')) {
                $table->boolean('is_for_adoption')->default(false)->after('status')->index();
            }
            if (!Schema::hasColumn('cats', 'adoption_status')) {
                $table->string('adoption_status')->default('available')->after('is_for_adoption'); // available, in_process, adopted
            }
            if (!Schema::hasColumn('cats', 'adoption_notes')) {
                $table->text('adoption_notes')->nullable()->after('adoption_status');
            }
            if (!Schema::hasColumn('cats', 'adoption_location')) {
                $table->string('adoption_location')->nullable()->after('adoption_notes');
            }
            if (!Schema::hasColumn('cats', 'adoption_fee_type')) {
                $table->string('adoption_fee_type')->default('Gratis (Bebas Biaya)')->after('adoption_location');
            }
            if (!Schema::hasColumn('cats', 'adoption_listed_at')) {
                $table->timestamp('adoption_listed_at')->nullable()->after('adoption_fee_type');
            }
        });

        // 2. Add adoption columns to stray_cat_surveys table
        Schema::table('stray_cat_surveys', function (Blueprint $table) {
            if (!Schema::hasColumn('stray_cat_surveys', 'is_for_adoption')) {
                $table->boolean('is_for_adoption')->default(false)->after('clinical_notes')->index();
            }
            if (!Schema::hasColumn('stray_cat_surveys', 'adoption_status')) {
                $table->string('adoption_status')->default('available')->after('is_for_adoption');
            }
            if (!Schema::hasColumn('stray_cat_surveys', 'adoption_notes')) {
                $table->text('adoption_notes')->nullable()->after('adoption_status');
            }
            if (!Schema::hasColumn('stray_cat_surveys', 'adoption_listed_at')) {
                $table->timestamp('adoption_listed_at')->nullable()->after('adoption_notes');
            }
        });

        // 3. Create adoption_applications table for tracking adoption requests from potential adopters
        Schema::create('adoption_applications', function (Blueprint $table) {
            $table->id();
            $table->string('application_code')->unique();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('cat_id')->nullable()->constrained('cats')->nullOnDelete();
            $table->foreignId('stray_cat_survey_id')->nullable()->constrained('stray_cat_surveys')->nullOnDelete();
            $table->string('cat_source')->default('member_cat'); // member_cat, stray_survey
            $table->string('cat_name');
            $table->string('applicant_name');
            $table->string('applicant_email');
            $table->string('applicant_phone');
            $table->string('applicant_city')->nullable();
            $table->text('applicant_address')->nullable();
            $table->string('housing_type')->nullable(); // Rumah Sendiri, Kontrakan, Kost
            $table->string('has_other_pets')->nullable();
            $table->boolean('has_family_consent')->default(true);
            $table->text('commitment_notes')->nullable();
            $table->string('status')->default('pending'); // pending, reviewing, approved, rejected, completed, cancelled
            $table->text('admin_notes')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();

            // Indexes for fast searching
            $table->index(['status', 'created_at']);
            $table->index(['cat_source', 'cat_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('adoption_applications');

        Schema::table('stray_cat_surveys', function (Blueprint $table) {
            $cols = ['is_for_adoption', 'adoption_status', 'adoption_notes', 'adoption_listed_at'];
            foreach ($cols as $c) {
                if (Schema::hasColumn('stray_cat_surveys', $c)) {
                    $table->dropColumn($c);
                }
            }
        });

        Schema::table('cats', function (Blueprint $table) {
            $cols = ['is_for_adoption', 'adoption_status', 'adoption_notes', 'adoption_location', 'adoption_fee_type', 'adoption_listed_at'];
            foreach ($cols as $c) {
                if (Schema::hasColumn('cats', $c)) {
                    $table->dropColumn($c);
                }
            }
        });
    }
};
