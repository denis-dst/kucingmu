<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminUserManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_user_management_page(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $member = User::factory()->create(['name' => 'Budi Santoso', 'role' => 'member']);
        $dokter = User::factory()->create(['name' => 'drh. Siti', 'role' => 'dokter']);

        $response = $this->actingAs($admin)->get(route('admin.users.index'));

        $response->assertStatus(200);
        $response->assertSee('Budi Santoso');
        $response->assertSee('drh. Siti');
        $response->assertSee('Kelola Pengguna');
    }

    public function test_non_admin_cannot_view_user_management_page(): void
    {
        $member = User::factory()->create(['role' => 'member']);

        $response = $this->actingAs($member)->get(route('admin.users.index'));

        $response->assertStatus(403);
    }

    public function test_admin_can_hire_or_update_member_role_to_volunteer(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $member = User::factory()->create(['name' => 'Ahmad Relawan', 'role' => 'member']);

        $response = $this->actingAs($admin)->put(route('admin.users.update-role', $member->id), [
            'role' => 'volunteer',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'id' => $member->id,
            'role' => 'volunteer',
        ]);
    }

    public function test_admin_can_hire_or_update_member_role_to_dokter(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $member = User::factory()->create(['name' => 'Calon Dokter', 'role' => 'member']);

        $response = $this->actingAs($admin)->put(route('admin.users.update-role', $member->id), [
            'role' => 'dokter',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'id' => $member->id,
            'role' => 'dokter',
        ]);
    }

    public function test_admin_cannot_change_their_own_role(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->put(route('admin.users.update-role', $admin->id), [
            'role' => 'member',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');

        $this->assertDatabaseHas('users', [
            'id' => $admin->id,
            'role' => 'admin',
        ]);
    }

    public function test_admin_can_impersonate_a_member_and_leave_impersonation(): void
    {
        $admin = User::factory()->create(['name' => 'Super Admin', 'role' => 'admin']);
        $member = User::factory()->create(['name' => 'Aisyah Member', 'role' => 'member']);

        // 1. Impersonate member
        $response = $this->actingAs($admin)->post(route('admin.users.impersonate', $member->id));

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($member);
        $this->assertEquals($admin->id, session('impersonator_id'));

        // 2. Leave impersonation
        $leaveResponse = $this->actingAs($member)->post(route('impersonate.leave'));

        $leaveResponse->assertRedirect(route('admin.users.index'));
        $this->assertAuthenticatedAs($admin);
        $this->assertNull(session('impersonator_id'));
    }

    public function test_admin_can_impersonate_a_dokter(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $dokter = User::factory()->create(['name' => 'drh. Fajar', 'role' => 'dokter']);

        $response = $this->actingAs($admin)->post(route('admin.users.impersonate', $dokter->id));

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($dokter);
        $this->assertEquals($admin->id, session('impersonator_id'));
    }

    public function test_non_admin_cannot_impersonate_other_users(): void
    {
        $member1 = User::factory()->create(['role' => 'member']);
        $member2 = User::factory()->create(['role' => 'member']);

        $response = $this->actingAs($member1)->post(route('admin.users.impersonate', $member2->id));

        $response->assertStatus(403);
    }
}
