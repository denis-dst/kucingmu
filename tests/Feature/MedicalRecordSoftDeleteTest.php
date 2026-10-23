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

    public function test_soft_deleting_medical_record_cancels_appointment_and_removes_from_queue(): void
    {
        $appointment = Appointment::create([
            'cat_id' => $this->cat->id,
            'date' => now()->toDateString(),
            'time_slot' => 'On-site Registration',
            'status' => 'checked_in',
            'notes' => 'Registrasi langsung di lokasi event.',
        ]);

        $record = MedicalRecord::create([
            'record_number' => 'MR-202610-0005',
            'appointment_id' => $appointment->id,
            'cat_id' => $this->cat->id,
            'member_id' => $this->memberUser->id,
            'vet_id' => $this->doctorA->id,
            'service_type' => 'clinic',
            'status' => 'in_progress',
            'chief_complaint' => 'Registrasi langsung di lokasi event.',
            'weight' => 3.5,
            'temperature' => 38.5,
            'general_condition' => 'Sedang Diperiksa',
            'created_by' => $this->doctorA->id,
        ]);

        // Soft delete the medical record
        $response = $this->actingAs($this->doctorA)
            ->delete(route('dokter.medical-records.destroy', $record));

        $response->assertRedirect(route('dokter.medical-records.index'));

        // Appointment should be cancelled
        $appointment->refresh();
        $this->assertEquals('cancelled', $appointment->status);

        // Should not appear in queue
        $indexResponse = $this->actingAs($this->doctorA)
            ->get(route('dokter.medical-records.index'));

        $indexResponse->assertStatus(200);
        $indexResponse->assertDontSee('Mulai Pemeriksaan SOAP');
        $indexResponse->assertSee('Tidak ada antrian pasien untuk hari ini.');
    }

    public function test_lingering_appointment_with_trashed_record_is_automatically_cancelled_and_excluded_from_queue(): void
    {
        $appointment = Appointment::create([
            'cat_id' => $this->cat->id,
            'date' => now()->toDateString(),
            'time_slot' => 'On-site Registration',
            'status' => 'checked_in',
            'notes' => 'Registrasi langsung di lokasi event.',
        ]);

        $record = MedicalRecord::create([
            'record_number' => 'MR-202610-0006',
            'appointment_id' => $appointment->id,
            'cat_id' => $this->cat->id,
            'member_id' => $this->memberUser->id,
            'vet_id' => $this->doctorA->id,
            'service_type' => 'clinic',
            'status' => 'in_progress',
            'chief_complaint' => 'Registrasi langsung',
            'weight' => 3.5,
            'temperature' => 38.5,
            'general_condition' => 'Sedang Diperiksa',
            'created_by' => $this->doctorA->id,
        ]);

        // Directly soft-delete the record while leaving appointment in checked_in (simulating existing data bug)
        $record->delete();

        $this->assertEquals('checked_in', $appointment->fresh()->status);

        // Visiting index should auto-cancel the lingering appointment and exclude it
        $response = $this->actingAs($this->doctorA)
            ->get(route('dokter.medical-records.index'));

        $response->assertStatus(200);
        $this->assertEquals('cancelled', $appointment->fresh()->status);
        $response->assertSee('Tidak ada antrian pasien untuk hari ini.');
    }

    public function test_doctor_can_cancel_appointment_directly_from_queue(): void
    {
        $appointment = Appointment::create([
            'cat_id' => $this->cat->id,
            'date' => now()->toDateString(),
            'time_slot' => 'On-site Registration',
            'status' => 'checked_in',
            'notes' => 'Registrasi langsung di lokasi event.',
        ]);

        $response = $this->actingAs($this->doctorA)
            ->delete(route('dokter.appointments.cancel', $appointment));

        $response->assertRedirect(route('dokter.medical-records.index'));
        $response->assertSessionHas('success');

        $this->assertEquals('cancelled', $appointment->fresh()->status);
    }

    public function test_generating_record_number_accounts_for_soft_deleted_records_and_prevents_duplicate_collision(): void
    {
        $prefix = 'MR-' . date('Ym') . '-';
        $number7 = $prefix . '0007';
        $number8 = $prefix . '0008';

        // Create record 7
        $record7 = MedicalRecord::create([
            'record_number' => $number7,
            'cat_id' => $this->cat->id,
            'member_id' => $this->memberUser->id,
            'vet_id' => $this->doctorA->id,
            'service_type' => 'clinic',
            'status' => 'completed',
            'chief_complaint' => 'Vaksin',
            'weight' => 3.5,
            'temperature' => 38.5,
            'general_condition' => 'Sehat',
            'created_by' => $this->doctorA->id,
        ]);

        // Soft delete record 7
        $record7->delete();
        $this->assertSoftDeleted('medical_records', ['id' => $record7->id]);

        // Next generated record number MUST be 0008, NOT 0007
        $nextNumber = MedicalRecord::generateRecordNumber();
        $this->assertEquals($number8, $nextNumber);

        // Submitting store for new record should succeed without duplicate entry error
        $response = $this->actingAs($this->doctorA)
            ->post(route('dokter.medical-records.store'), [
                'cat_id' => $this->cat->id,
                'service_type' => 'clinic',
                'chief_complaint' => 'Diare',
                'clinic_name' => 'Klinik Hewan KucingMu',
            ]);

        $response->assertSessionDoesntHaveErrors();
        $newRecord = MedicalRecord::where('record_number', $number8)->first();
        $this->assertNotNull($newRecord);
        $response->assertRedirect(route('dokter.medical-records.examine', $newRecord));
    }
}
