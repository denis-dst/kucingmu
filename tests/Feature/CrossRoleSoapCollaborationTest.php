<?php

namespace Tests\Feature;

use App\Models\Cat;
use App\Models\KtamCard;
use App\Models\MedicalRecord;
use App\Models\MedicalRecordAuditLog;
use App\Models\MedicalRecordDiagnosis;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CrossRoleSoapCollaborationTest extends TestCase
{
    use RefreshDatabase;

    private User $verifikator;
    private User $doctorA;
    private User $doctorB;
    private User $member;
    private Cat $cat;

    protected function setUp(): void
    {
        parent::setUp();

        $this->verifikator = User::factory()->create([
            'name' => 'Verifikator Siti',
            'role' => 'verifikator',
            'roles' => ['verifikator'],
        ]);

        $this->doctorA = User::factory()->create([
            'name' => 'drh. Budi',
            'role' => 'dokter',
            'roles' => ['dokter'],
        ]);

        $this->doctorB = User::factory()->create([
            'name' => 'drh. Sarah',
            'role' => 'dokter',
            'roles' => ['dokter'],
        ]);

        $this->member = User::factory()->create([
            'name' => 'Member Ahmad',
            'role' => 'member',
            'roles' => ['member'],
        ]);

        $this->cat = Cat::create([
            'user_id' => $this->member->id,
            'name' => 'Oyen',
            'breed' => 'Domestik',
            'gender' => 'male',
            'status' => 'alive',
            'date_of_birth' => '2023-01-01',
            'wilayah_code' => '34',
            'unique_code' => '34.kcg.0001',
        ]);

        KtamCard::create([
            'cat_id' => $this->cat->id,
            'ktam_number' => '34.kcg.0001',
            'verified_by' => $this->verifikator->id,
            'verified_at' => now(),
            'issue_date' => now(),
            'status' => 'issued',
            'qr_code_payload' => 'payload_oyen_0001',
        ]);
    }

    public function test_verifikator_can_access_medical_records_index()
    {
        $response = $this->actingAs($this->verifikator)
            ->get(route('dokter.medical-records.index'));

        $response->assertStatus(200);
        $response->assertSee('Modul Rekam Medis Veteriner (SOAP)');
    }

    public function test_verifikator_can_initiate_new_soap_record()
    {
        $response = $this->actingAs($this->verifikator)
            ->post(route('dokter.medical-records.store'), [
                'cat_id' => $this->cat->id,
                'service_type' => 'clinic',
                'chief_complaint' => 'Nafsu makan menurun dan lesu',
            ]);

        $record = MedicalRecord::where('cat_id', $this->cat->id)->latest()->first();

        $this->assertNotNull($record);
        $response->assertRedirect(route('dokter.medical-records.examine', $record));
        $this->assertEquals($this->verifikator->id, $record->vet_id);
        $this->assertEquals($this->verifikator->id, $record->created_by);

        $this->assertDatabaseHas('medical_record_audit_logs', [
            'medical_record_id' => $record->id,
            'actor_id' => $this->verifikator->id,
            'action' => 'created',
        ]);
    }

    public function test_doctor_can_continue_and_save_steps_on_record_started_by_verifikator()
    {
        $record = MedicalRecord::create([
            'record_number' => MedicalRecord::generateRecordNumber(),
            'cat_id' => $this->cat->id,
            'member_id' => $this->member->id,
            'vet_id' => $this->verifikator->id,
            'created_by' => $this->verifikator->id,
            'service_type' => 'clinic',
            'status' => MedicalRecord::STATUS_IN_PROGRESS,
            'chief_complaint' => 'Batuk pilek',
            'weight' => 0.00,
            'temperature' => 0.0,
            'general_condition' => 'Dalam Pemeriksaan',
            'started_at' => now(),
        ]);

        $this->assertTrue($record->canBeEditedBy($this->doctorA));

        $examineResponse = $this->actingAs($this->doctorA)
            ->get(route('dokter.medical-records.examine', $record));

        $examineResponse->assertStatus(200);
        $examineResponse->assertSee($this->verifikator->name);

        $saveResponse = $this->actingAs($this->doctorA)
            ->post(route('dokter.medical-records.save-step', ['record' => $record->id, 'step' => 'objective']), [
                'weight' => 4.15,
                'temperature' => 38.6,
                'heart_rate' => 140,
                'respiratory_rate' => 28,
                'body_condition_score' => '3',
                'general_condition' => 'Aktif, hidrasi cukup',
            ]);

        $saveResponse->assertRedirect(route('dokter.medical-records.examine', ['record' => $record->id, 'step' => 3]));

        $record->refresh();
        $this->assertEquals(4.15, (float) $record->weight);
        $this->assertEquals(38.6, (float) $record->temperature);

        $this->assertDatabaseHas('medical_record_audit_logs', [
            'medical_record_id' => $record->id,
            'actor_id' => $this->doctorA->id,
            'action' => 'updated_draft',
        ]);
    }

    public function test_doctor_b_can_continue_and_finalize_record_started_by_doctor_a()
    {
        $record = MedicalRecord::create([
            'record_number' => MedicalRecord::generateRecordNumber(),
            'cat_id' => $this->cat->id,
            'member_id' => $this->member->id,
            'vet_id' => $this->doctorA->id,
            'created_by' => $this->doctorA->id,
            'service_type' => 'clinic',
            'status' => MedicalRecord::STATUS_IN_PROGRESS,
            'chief_complaint' => 'Luka cakar',
            'weight' => 3.80,
            'temperature' => 38.4,
            'general_condition' => 'Sedang diperiksa',
            'started_at' => now(),
        ]);

        MedicalRecordDiagnosis::create([
            'medical_record_id' => $record->id,
            'diagnosis_name' => 'Vulnus laceratum auris dextra',
            'is_primary' => true,
            'certainty' => 'confirmed',
        ]);

        $this->assertTrue($record->canBeEditedBy($this->doctorB));

        $response = $this->actingAs($this->doctorB)
            ->post(route('dokter.medical-records.finalize', $record));

        $response->assertRedirect(route('dokter.medical-records.show', $record));

        $record->refresh();
        $this->assertEquals(MedicalRecord::STATUS_COMPLETED, $record->status);
        $this->assertEquals($this->doctorA->id, $record->vet_id);

        $this->assertDatabaseHas('medical_record_audit_logs', [
            'medical_record_id' => $record->id,
            'actor_id' => $this->doctorB->id,
            'action' => 'finalized',
        ]);
    }

    public function test_verifikator_can_continue_and_save_assessment_on_record_started_by_doctor()
    {
        $record = MedicalRecord::create([
            'record_number' => MedicalRecord::generateRecordNumber(),
            'cat_id' => $this->cat->id,
            'member_id' => $this->member->id,
            'vet_id' => $this->doctorA->id,
            'created_by' => $this->doctorA->id,
            'service_type' => 'clinic',
            'status' => MedicalRecord::STATUS_IN_PROGRESS,
            'chief_complaint' => 'Skrining kesehatan rutin',
            'weight' => 3.50,
            'temperature' => 38.2,
            'general_condition' => 'Baik',
            'started_at' => now(),
        ]);

        $this->assertTrue($record->canBeEditedBy($this->verifikator));

        $response = $this->actingAs($this->verifikator)
            ->post(route('dokter.medical-records.save-step', ['record' => $record->id, 'step' => 'assessment']), [
                'primary_diagnosis' => 'Pemeriksaan Rutin Sehat',
                'primary_certainty' => 'confirmed',
                'primary_severity' => 'mild',
                'clinical_reasoning' => 'Kondisi klinis baik',
            ]);

        $response->assertRedirect(route('dokter.medical-records.examine', ['record' => $record->id, 'step' => 4]));

        $this->assertDatabaseHas('medical_record_diagnoses', [
            'medical_record_id' => $record->id,
            'diagnosis_name' => 'Pemeriksaan Rutin Sehat',
            'is_primary' => true,
        ]);

        $this->assertDatabaseHas('medical_record_audit_logs', [
            'medical_record_id' => $record->id,
            'actor_id' => $this->verifikator->id,
            'action' => 'updated_draft',
        ]);
    }

    public function test_unauthorized_member_cannot_access_or_edit_soap()
    {
        $record = MedicalRecord::create([
            'record_number' => MedicalRecord::generateRecordNumber(),
            'cat_id' => $this->cat->id,
            'member_id' => $this->member->id,
            'vet_id' => $this->doctorA->id,
            'created_by' => $this->doctorA->id,
            'service_type' => 'clinic',
            'status' => MedicalRecord::STATUS_IN_PROGRESS,
            'chief_complaint' => 'Keluhan',
            'weight' => 3.00,
            'temperature' => 38.0,
            'general_condition' => 'Sedang diperiksa',
            'started_at' => now(),
        ]);

        $this->assertFalse($record->canBeEditedBy($this->member));

        $response = $this->actingAs($this->member)
            ->get(route('dokter.medical-records.examine', $record));

        $response->assertStatus(403);
    }
}
