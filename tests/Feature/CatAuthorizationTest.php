<?php

namespace Tests\Feature;

use App\Models\Cat;
use App\Models\KtamCard;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CatAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    private User $memberA;
    private User $memberB;
    private User $verifikatorUser;
    private User $adminUser;
    private Cat $catA;

    protected function setUp(): void
    {
        parent::setUp();

        $this->memberA = User::factory()->create([
            'role' => 'member',
            'roles' => ['member'],
        ]);

        $this->memberB = User::factory()->create([
            'role' => 'member',
            'roles' => ['member'],
        ]);

        $this->verifikatorUser = User::factory()->create([
            'role' => 'verifikator',
            'roles' => ['verifikator', 'member'],
        ]);

        $this->adminUser = User::factory()->create([
            'role' => 'admin',
            'roles' => ['admin', 'member'],
        ]);

        $this->catA = Cat::create([
            'user_id' => $this->memberA->id,
            'name' => 'Mochi',
            'breed' => 'Persia',
            'gender' => 'male',
            'status' => 'alive',
            'date_of_birth' => '2023-01-01',
            'wilayah_code' => '34',
            'unique_code' => '34.kcg.0099',
        ]);

        KtamCard::create([
            'cat_id' => $this->catA->id,
            'ktam_number' => '34.kcg.0099',
            'verified_by' => $this->verifikatorUser->id,
            'verified_at' => now(),
            'issue_date' => now(),
            'status' => 'issued',
            'qr_code_payload' => 'payload',
        ]);
    }

    public function test_member_can_access_edit_page_of_their_own_cat(): void
    {
        $response = $this->actingAs($this->memberA)->get(route('cat.edit', $this->catA->id));
        $response->assertStatus(200);
    }

    public function test_member_cannot_access_edit_page_of_another_members_cat(): void
    {
        $response = $this->actingAs($this->memberB)->get(route('cat.edit', $this->catA->id));
        $response->assertStatus(403);
    }

    public function test_member_cannot_update_another_members_cat(): void
    {
        $response = $this->actingAs($this->memberB)->put(route('cat.update', $this->catA->id), [
            'name' => 'Hacked Name',
            'breed' => 'Persia',
            'gender' => 'male',
            'date_of_birth' => '2023-01-01',
        ]);

        $response->assertStatus(403);
        $this->assertDatabaseMissing('cats', [
            'id' => $this->catA->id,
            'name' => 'Hacked Name',
        ]);
    }

    public function test_member_cannot_delete_another_members_cat(): void
    {
        $response = $this->actingAs($this->memberB)->delete(route('cat.destroy', $this->catA->id));
        $response->assertStatus(403);
        $this->assertNotSoftDeleted('cats', ['id' => $this->catA->id]);
    }

    public function test_member_cannot_toggle_status_of_another_members_cat(): void
    {
        $response = $this->actingAs($this->memberB)->post(route('cat.toggle-status', $this->catA->id));
        $response->assertStatus(403);
    }

    public function test_member_cannot_preview_ktam_of_another_members_cat(): void
    {
        $response = $this->actingAs($this->memberB)->get(route('ktam.preview', $this->catA->id));
        $response->assertStatus(403);
    }

    public function test_member_cannot_download_ktam_of_another_members_cat(): void
    {
        $response = $this->actingAs($this->memberB)->get(route('ktam.download', $this->catA->id));
        $response->assertStatus(403);
    }

    public function test_verifikator_in_member_workspace_cannot_edit_other_cats(): void
    {
        // When active_role is member
        $response = $this->actingAs($this->verifikatorUser)
            ->withSession(['active_role' => 'member'])
            ->get(route('cat.edit', $this->catA->id));

        $response->assertStatus(403);
    }

    public function test_verifikator_in_verifikator_workspace_can_edit_cats(): void
    {
        $response = $this->actingAs($this->verifikatorUser)
            ->withSession(['active_role' => 'verifikator'])
            ->get(route('cat.edit', $this->catA->id));

        $response->assertStatus(200);
    }

    public function test_admin_in_member_workspace_cannot_edit_other_cats(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->withSession(['active_role' => 'member'])
            ->get(route('cat.edit', $this->catA->id));

        $response->assertStatus(403);
    }

    public function test_admin_in_admin_workspace_can_edit_cats(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->withSession(['active_role' => 'admin'])
            ->get(route('cat.edit', $this->catA->id));

        $response->assertStatus(200);
    }

    public function test_member_cannot_book_appointment_for_another_members_cat(): void
    {
        $response = $this->actingAs($this->memberB)->post(route('appointment.store'), [
            'cat_id' => $this->catA->id,
            'date' => now()->addDays(2)->format('Y-m-d'),
            'time_slot' => '10:00 - 11:00',
        ]);

        $response->assertStatus(403);
    }

    public function test_member_cannot_manage_photos_of_another_members_cat(): void
    {
        $photo = \App\Models\CatPhoto::create([
            'cat_id' => $this->catA->id,
            'photo_path' => 'cats/photo1.jpg',
            'label' => 'Tampak Depan',
            'is_primary' => false,
        ]);

        // Attempt set primary
        $response = $this->actingAs($this->memberB)->post(route('photos.set-primary', $photo->id));
        $response->assertStatus(403);

        // Attempt delete photo
        $response = $this->actingAs($this->memberB)->delete(route('photos.destroy', $photo->id));
        $response->assertStatus(403);
    }

    public function test_member_cannot_view_medical_summary_of_another_members_cat(): void
    {
        $record = \App\Models\MedicalRecord::create([
            'cat_id' => $this->catA->id,
            'member_id' => $this->memberA->id,
            'vet_id' => $this->adminUser->id,
            'service_type' => 'clinical_checkup',
            'weight' => 4.2,
            'temperature' => 38.5,
            'general_condition' => 'Sehat dan aktif',
            'status' => 'completed',
        ]);

        $response = $this->actingAs($this->memberB)->get(route('member.medical-records.summary', $record->id));
        $response->assertStatus(403);

        $responseOwner = $this->actingAs($this->memberA)->get(route('member.medical-records.summary', $record->id));
        $responseOwner->assertStatus(200);
    }
}
