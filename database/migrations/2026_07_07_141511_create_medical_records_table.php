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
        Schema::create('medical_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('appointment_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('cat_id')->constrained()->onDelete('cascade');
            $table->foreignId('vet_id')->constrained('users')->onDelete('cascade');
            $table->decimal('weight', 5, 2); // e.g. 12.34 kg
            $table->decimal('temperature', 4, 1); // e.g. 38.5 C
            $table->string('general_condition');
            $table->boolean('deworming_given')->default(false);
            $table->boolean('anti_flea_given')->default(false);
            $table->boolean('supplement_given')->default(false);
            $table->text('treatment_notes')->nullable();
            $table->text('recommendation')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medical_records');
    }
};
