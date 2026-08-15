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
        Schema::create('cat_photos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cat_id')->constrained()->onDelete('cascade');
            $table->string('photo_path');
            $table->string('label')->default('Tampak Depan'); // e.g. Tampak Depan, Tampak Samping, Tampak Atas, Lainnya
            $table->boolean('is_primary')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cat_photos');
    }
};
