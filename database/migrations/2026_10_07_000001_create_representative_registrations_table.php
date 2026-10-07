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
        Schema::create('representative_registrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('registration_number')->unique();
            
            // Personal Information
            $table->string('name');
            $table->string('nbm')->nullable();
            $table->date('birth_date');
            $table->string('email');
            $table->string('whatsapp_number');
            $table->string('instagram_username')->nullable();
            
            // Regional Information
            $table->string('province_name');
            $table->string('city_name');
            $table->string('district_name');
            $table->string('village_name');
            
            // Geolocation Tagging
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->text('formatted_address')->nullable();
            
            // Organization & Affiliation
            $table->string('muhammadiyah_active_leadership');
            $table->string('sk_pimpinan_document_path');
            $table->string('ktam_document_path');
            
            // Animal Welfare Knowledge
            $table->text('animal_welfare_essay');
            
            // Consent & Privacy
            $table->boolean('privacy_agreed')->default(false);
            
            // Review & Processing Status
            $table->string('status')->default('pending'); // pending, reviewed, approved, rejected
            $table->text('admin_notes')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            
            $table->timestamps();
            
            // Indexes
            $table->index('status');
            $table->index('province_name');
            $table->index('city_name');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('representative_registrations');
    }
};
