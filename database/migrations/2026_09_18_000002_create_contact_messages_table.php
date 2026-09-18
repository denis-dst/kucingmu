<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('contact_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('name');
            $table->string('email');
            $table->string('phone', 50)->nullable();
            $table->string('subject');
            $table->text('message');
            $table->string('status', 20)->default('unread'); // unread, read, responded
            $table->text('admin_response')->nullable();
            $table->foreignId('responded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('responded_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'created_at']);
        });

        // Seed or update contact_email and office_address in app_settings
        $defaultEmail = 'bidkes.immdiy@gmail.com / kucingmuhammadiyah@gmail.com';
        $defaultAddress = 'Jl. Gedongkuning No.130 B, Rejowinangun, Kec. Kotagede, Kota Yogyakarta, Daerah Istimewa Yogyakarta 55171';

        DB::table('app_settings')->updateOrInsert(
            ['key' => 'contact_email'],
            [
                'value' => $defaultEmail,
                'label' => 'Email Kontak Resmi',
                'type' => 'text',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        DB::table('app_settings')->updateOrInsert(
            ['key' => 'office_address'],
            [
                'value' => $defaultAddress,
                'label' => 'Alamat Kantor Sekretariat',
                'type' => 'text',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contact_messages');
    }
};
