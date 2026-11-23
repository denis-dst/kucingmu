<?php

namespace Tests\Feature;

use App\Models\Cat;
use App\Models\MedicalRecord;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DokterDashboardDraftFilterTest extends TestCase
{
    use RefreshDatabase;

    protected User $doctor;
    protected User $member;
    protected Cat $cat;

    protected function setUp(): void
    {
        parent::setUp();

        $this->doctor = User::factory()->create([
            'role' => 'dokter',
            'roles' => ['dokter', 'member'],
        ]);

        $this->member = User::factory()->create([
            'role' => 'member',
            'roles' => ['member'],
        ]);

        $this->cat = Cat::create([
            'user_id' => $this->member->id,
            'name' => 'Oren',
            'breed' => 'Domestik',
            'gender' => 'male',
            'status' => 'alive',
            'date_of_birth' => '2023-01-01',
            'wilayah_code' => '34',
        ]);
    }

    public function test_dokter_dashboard_defaults_to_today_filter_for_in_progress_drafts()
    {
        // 1. Draft created TODAY
        $draftToday = MedicalRecord::create([
            'record_number' => 'MR-DRAFT-TODAY-01',
            'cat_id' => $this->cat->id,
            'member_id' => $this->member->id,
            'vet_id' => $this->doctor->id,
            'created_by' => $this->doctor->id,
            'service_type' => 'clinic',
            'status' => 'in_progress',
            'chief_complaint' => 'Pemeriksaan draft hari ini',
            'weight' => 3.2,
            'temperature' => 38.2,
            'general_condition' => 'Active',
            'created_at' => Carbon::today()->addHours(8),
            'updated_at' => Carbon::today()->addHours(8),
        ]);

        // 2. Draft created YESTERDAY
        $draftYesterday = MedicalRecord::create([
            'record_number' => 'MR-DRAFT-YESTERDAY-99',
            'cat_id' => $this->cat->id,
            'member_id' => $this->member->id,
            'vet_id' => $this->doctor->id,
            'created_by' => $this->doctor->id,
            'service_type' => 'clinic',
            'status' => 'in_progress',
            'chief_complaint' => 'Pemeriksaan draft kemarin',
            'weight' => 3.2,
            'temperature' => 38.2,
            'general_condition' => 'Active',
        ]);
        $draftYesterday->timestamps = false;
        $draftYesterday->created_at = Carbon::yesterday()->addHours(14);
        $draftYesterday->saveQuietly();

        $response = $this->actingAs($this->doctor)->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Pemeriksaan Berjalan (Draft Belum Selesai)');
        $response->assertSee('MR-DRAFT-TODAY-01');

        // Verify that inProgressDrafts collection only contains today's draft, not yesterday's
        $drafts = $response->viewData('inProgressDrafts');
        $this->assertTrue($drafts->contains('record_number', 'MR-DRAFT-TODAY-01'));
        $this->assertFalse($drafts->contains('record_number', 'MR-DRAFT-YESTERDAY-99'));
    }

    public function test_dokter_dashboard_shows_all_drafts_when_draft_filter_all_selected()
    {
        // Draft created 3 days ago
        $draftPast = MedicalRecord::create([
            'record_number' => 'MR-DRAFT-PAST-77',
            'cat_id' => $this->cat->id,
            'member_id' => $this->member->id,
            'vet_id' => $this->doctor->id,
            'created_by' => $this->doctor->id,
            'service_type' => 'clinic',
            'status' => 'draft',
            'chief_complaint' => 'Draft lama',
            'weight' => 3.0,
            'temperature' => 38.0,
            'general_condition' => 'Active',
        ]);
        $draftPast->timestamps = false;
        $draftPast->created_at = Carbon::now()->subDays(3);
        $draftPast->saveQuietly();

        $response = $this->actingAs($this->doctor)->get(route('dashboard', ['draft_filter' => 'all']));

        $response->assertStatus(200);
        $response->assertSee('Semua Draft');
        $response->assertSee('MR-DRAFT-PAST-77');
    }
}
