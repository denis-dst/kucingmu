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
        Schema::create('ktam_cards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cat_id')->unique()->constrained()->onDelete('cascade');
            $table->string('ktam_number')->unique();
            $table->date('issue_date');
            $table->text('qr_code_payload');
            $table->boolean('is_printed')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ktam_cards');
    }
};
