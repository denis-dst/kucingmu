<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminUserFarmasiRoleTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_users_filtered_by_farmasi_role(): void
    {
        $admin = User::create([
            'name' => 'Admin Utama',
            'email' => 'admin@kucingmu.test',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'roles' => ['admin', 'member'],
        ]);

        $farmasiUser = User::create([
            'name' => 'Apoteker KucingMu',
            'email' => 'apoteker@kucingmu.test',
            'password' => bcrypt('password'),
            'role' => 'farmasi',
            'roles' => ['farmasi', 'member'],
        ]);

        $regularMember = User::create([
            'name' => 'Member Biasa',
            'email' => 'member@kucingmu.test',
            'password' => bcrypt('password'),
            'role' => 'member',
            'roles' => ['member'],
        ]);

        $response = $this->actingAs($admin)->get(route('admin.users.index', ['role' => 'farmasi']));

        $response->assertStatus(200);
        $response->assertSee('Apoteker KucingMu');
        $response->assertDontSee('Member Biasa');
        $response->assertSee('Farmasi');
    }

    public function test_admin_can_assign_farmasi_role_to_user(): void
    {
        $admin = User::create([
            'name' => 'Admin Utama',
            'email' => 'admin@kucingmu.test',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'roles' => ['admin', 'member'],
        ]);

        $targetUser = User::create([
            'name' => 'Calon Apoteker',
            'email' => 'calon@kucingmu.test',
            'password' => bcrypt('password'),
            'role' => 'member',
            'roles' => ['member'],
        ]);

        $response = $this->actingAs($admin)->put(route('admin.users.update-role', $targetUser), [
            'roles' => ['member', 'farmasi'],
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $targetUser->refresh();
        $this->assertTrue($targetUser->isFarmasi());
        $this->assertContains('farmasi', $targetUser->getAllRoles());
        $this->assertEquals('farmasi', $targetUser->role);
    }
}
