<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stray_cat_surveys', function (Blueprint $table) {
            $table->id();
            $table->foreignId('volunteer_id')->constrained('users')->cascadeOnDelete();
            $table->dateTime('surveyed_at');
            $table->string('campus_location');
            $table->string('zone');
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->enum('weather', ['cerah', 'berawan', 'hujan'])->nullable();
            $table->unsignedInteger('cats_observed');
            $table->unsignedInteger('cats_with_ear_tip')->default(0);
            $table->unsignedInteger('cats_needing_attention')->default(0);
            $table->string('food_source')->nullable();
            $table->text('notes')->nullable();
            $table->string('photo_path')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stray_cat_surveys');
    }
};
