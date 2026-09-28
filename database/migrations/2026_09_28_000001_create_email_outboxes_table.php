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
        Schema::create('email_outboxes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sender_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('contact_message_id')->nullable()->constrained('contact_messages')->nullOnDelete();
            $table->string('recipient_email');
            $table->string('recipient_name')->nullable();
            $table->string('subject');
            $table->longText('body');
            $table->string('mail_type', 50)->default('direct_compose'); // direct_compose, contact_reply, registration_notice, test_smtp, system
            $table->string('mailer', 50)->default('smtp');
            $table->string('status', 20)->default('sent'); // sent, failed, queued
            $table->text('error_message')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'created_at']);
            $table->index('recipient_email');
            $table->index('mail_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('email_outboxes');
    }
};
