<?php

namespace Tests\Feature;

use App\Models\PtmaCatCensus;
use App\Models\StrayCatSurvey;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VolunteerDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_volunteer_can_access_dashboard_without_class_not_found_error(): void
    {
        $volunteer = User::create([
            'name' => 'Relawan Sensus',
            'email' => 'relawan@kucingmu.test',
            'password' => bcrypt('password'),
            'role' => 'volunteer',
            'roles' => ['volunteer', 'member'],
        ]);

        $census = PtmaCatCensus::create([
            'id_kucing' => 'UMM-001',
            'volunteer_id' => $volunteer->id,
            'kampus' => 'Universitas Muhammadiyah Malang',
            'zona' => 'Gedung Kuliah Bersama I',
            'latitude' => -7.9826,
            'longitude' => 112.6308,
            'usia' => 'dewasa',
            'gender' => 'jantan',
            'warna' => 'oranye',
            'bcs' => 'ideal',
            'jenis_pakan' => 'dry_food',
            'ancaman' => 'tidak_ada',
        ]);

        $survey = StrayCatSurvey::create([
            'volunteer_id' => $volunteer->id,
            'surveyed_at' => now(),
            'campus_location' => 'Universitas Muhammadiyah Malang',
            'zone' => 'Zona 1',
            'latitude' => -7.9827,
            'longitude' => 112.6309,
            'cats_observed' => 5,
        ]);

        $response = $this->actingAs($volunteer)->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertViewIs('volunteer.dashboard');
        $response->assertSee('Relawan');
        $response->assertViewHas('recentCensus');
        $response->assertViewHas('recentSurveys');
        $response->assertViewHas('totalCensusCount', 1);
        $response->assertViewHas('totalSurveyCount', 1);
    }
}
