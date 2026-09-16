<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasColumn('users', 'roles')) {
            Schema::table('users', function (Blueprint $table) {
                $table->json('roles')->nullable()->after('role');
            });
        }

        // Initialize roles for existing users based on their primary role
        $users = DB::table('users')->select('id', 'role', 'roles')->get();
        foreach ($users as $u) {
            $existingRole = strtolower(trim($u->role ?? 'member'));
            $initialRoles = array_values(array_unique(array_filter([$existingRole, 'member'])));
            DB::table('users')
                ->where('id', $u->id)
                ->update(['roles' => json_encode($initialRoles)]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('users', 'roles')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('roles');
            });
        }
    }
};
