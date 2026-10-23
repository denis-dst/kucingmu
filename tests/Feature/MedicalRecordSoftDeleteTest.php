<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\Cat;
use App\Models\KtamCard;
use App\Models\MedicalRecord;
use App\Models\MedicalRecordAuditLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MedicalRecordSoftDeleteTest extends TestCase
{
    use RefreshDatabase;

    private User $doctorA;
    private User $doctorB;
    private User $adminUser;
    private User $memberUser;
    private Cat $cat;

    protected function setUp(): void
    {
        parent::setUp();

        $this->doctorA = User::factory()->create([
            'role' => 'dokter',
            'roles' => ['dokter'],
        ]);

        $this->doctorB = User::factory()->create([
            'role' => 'dokter',
            'roles' => ['dokter'],
        ]);

        $this->adminUser = User::factory()->create([
            'role' => 'admin',
            'roles' => ['admin'],
        ]);

        $this->memberUser = User::factory()->create([
            'role' => 'member',
            'roles' => ['member'],
        ]);

        $this->cat = Cat::create([
            'user_id' => $this->memberUser->id,
            'name' => 'Milo',
            'breed' => 'Domestik',
            'gender' => 'male',
            'status' => 'alive',
            'date_of_birth' => '2023-01-01',
            'wilayah_code' => '34',
            'unique_code' => '34.kcg.0088',
        ]);

        KtamCard::create([
            'cat_id' => $this->cat->id,
            'ktam_number' => '34.kcg.0088',
            'verified_by' => $this->adminUser->id,
            'verified_at' => now(),
            'issue_date' => now(),
            'status' => 'issued',
            'qr_code_payload' => 'payload',
        ]);
    }

    public function test_doctor_can_soft_delete_their_own_medical_record(): void
    {
        $record = MedicalRecord::create([
            'record_number' => 'MR-202610-0001',
            'cat_id' => $this->cat->id,
            'member_id' => $this->memberUser->id,
            'vet_id' => $this->doctorA->id,
            'service_type' => 'clinic',
            'status' => 'in_progress',
            'chief_complaint' => 'Nafsu makan menurun',
            'weight' => 3.5,
            'temperature' => 38.5,
            'general_condition' => 'Sedang Diperiksa',
            'created_by' => $this->doctorA->id,
        ]);

        $response = $this->actingAs($this->doctorA)
            ->delete(route('dokter.medical-records.destroy', $record));

        $response->assertRedirect(route('dokter.medical-records.index'));
        $response->assertSessionHas('success');

        // Verify soft deletion
        $this->assertSoftDeleted('medical_records', [
            'id' => $record->id,
        ]);

        $deletedRecord = MedicalRecord::withTrashed()->find($record->id);
        $this->assertNotNull($deletedRecord->deleted_at);
        $this->assertEquals($this->doctorA->id, $deletedRecord->deleted_by);

        // Verify audit log
        $this->assertDatabaseHas('medical_record_audit_logs', [
            'medical_record_id' => $record->id,
            'action' => 'deleted',
            'actor_id' => $this->doctorA->id,
        ]);
    }

    public function test_admin_can_soft_delete_any_medical_record(): void
    {
        $record = MedicalRecord::create([
            'record_number' => 'MR-202610-0002',
            'cat_id' => $this->cat->id,
            'member_id' => $this->memberUser->id,
            'vet_id' => $this->doctorA->id,
            'service_type' => 'clinic',
            'status' => 'completed',
            'chief_complaint' => 'Demam',
            'weight' => 4.0,
            'temperature' => 39.0,
            'general_condition' => 'Selesai',
            'created_by' => $this->doctorA->id,
        ]);

        $response = $this->actingAs($this->adminUser)
            ->delete(route('dokter.medical-records.destroy', $record));

        $response->assertRedirect(route('dokter.medical-records.index'));
        $this->assertSoftDeleted('medical_records', [
            'id' => $record->id,
        ]);
    }

    public function test_doctor_cannot_delete_another_doctors_record(): void
    {
        $record = MedicalRecord::create([
            'record_number' => 'MR-202610-0003',
            'cat_id' => $this->cat->id,
            'member_id' => $this->memberUser->id,
            'vet_id' => $this->doctorA->id,
            'service_type' => 'clinic',
            'status' => 'in_progress',
            'chief_complaint' => 'Flu',
            'weight' => 3.2,
            'temperature' => 38.6,
            'general_condition' => 'Dalam Pemeriksaan',
            'created_by' => $this->doctorA->id,
        ]);

        $response = $this->actingAs($this->doctorB)
            ->delete(route('dokter.medical-records.destroy', $record));

        $response->assertStatus(403);
        $this->assertNotSoftDeleted('medical_records', [
            'id' => $record->id,
        ]);
    }

    public function test_soft_deleted_records_do_not_appear_in_index(): void
    {
        $record = MedicalRecord::create([
            'record_number' => 'MR-202610-0004',
            'cat_id' => $this->cat->id,
            'member_id' => $this->memberUser->id,
            'vet_id' => $this->doctorA->id,
            'service_type' => 'clinic',
            'status' => 'completed',
            'chief_complaint' => 'Vaksin',
            'weight' => 3.8,
            'temperature' => 38.2,
            'general_condition' => 'Sehat',
            'created_by' => $this->doctorA->id,
        ]);

        // Soft delete it
        $record->delete();

        $response = $this->actingAs($this->doctorA)
            ->get(route('dokter.medical-records.index'));

        $response->assertStatus(200);
        $response->assertDontSee('MR-202610-0004');
    }
}
