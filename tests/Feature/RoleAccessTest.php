<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login()
    {
        $response = $this->get('/dashboard');
        $response->assertRedirect('/login');
    }

    public function test_member_can_access_dashboard()
    {
        $user = User::factory()->create(['role' => 'member']);

        $response = $this->actingAs($user)->get('/dashboard');
        $response->assertStatus(200);
        $response->assertSee('Panel Pemilik Kucing');
    }

    public function test_dokter_can_access_dashboard()
    {
        $user = User::factory()->create(['role' => 'dokter']);

        $response = $this->actingAs($user)->get('/dashboard');
        $response->assertStatus(200);
        $response->assertSee('Portal Dokter Hewan');
    }

    public function test_volunteer_can_access_dashboard()
    {
        $user = User::factory()->create(['role' => 'volunteer']);

        $response = $this->actingAs($user)->get('/dashboard');
        $response->assertStatus(200);
        $response->assertSee('Portal Relawan Lapangan');
    }

    public function test_admin_can_access_dashboard()
    {
        $user = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($user)->get('/dashboard');
        $response->assertStatus(200);
        $response->assertSee('Portal Administrator Utama');
    }

    public function test_member_cannot_access_admin_routes()
    {
        $user = User::factory()->create(['role' => 'member']);

        $response = $this->actingAs($user)->get('/export-data');
        $response->assertStatus(403);
    }
}
