<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\Cat;
use App\Models\MedicalRecord;
use App\Models\MedicalRecordDiagnosis;
use App\Models\Medicine;
use App\Models\Prescription;
use App\Models\PrescriptionItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MemberMedicalRecordViewAndPrintTest extends TestCase
{
    use RefreshDatabase;

    protected User $memberA;
    protected User $memberB;
    protected User $doctor;
    protected Cat $catA;
    protected Cat $catB;

    protected function setUp(): void
    {
        parent::setUp();

        $this->memberA = User::factory()->create([
            'role' => 'member',
            'roles' => ['member'],
            'name' => 'Ahmad Fauzi',
        ]);

        $this->memberB = User::factory()->create([
            'role' => 'member',
            'roles' => ['member'],
            'name' => 'Budi Santoso',
        ]);

        $this->doctor = User::factory()->create([
            'role' => 'dokter',
            'roles' => ['dokter'],
            'name' => 'drh. Siti Rahma',
        ]);

        $this->catA = Cat::create([
            'user_id' => $this->memberA->id,
            'name' => 'Milo',
            'breed' => 'Domestik',
            'gender' => 'male',
            'status' => 'alive',
            'date_of_birth' => now()->subYears(2),
        ]);

        $this->catB = Cat::create([
            'user_id' => $this->memberB->id,
            'name' => 'Luna',
            'breed' => 'Persia',
            'gender' => 'female',
            'status' => 'alive',
            'date_of_birth' => now()->subYear(),
        ]);
    }

    public function test_member_can_view_finalized_medical_record_summary_of_own_cat()
    {
        $record = MedicalRecord::create([
            'record_number' => MedicalRecord::generateRecordNumber(),
            'cat_id' => $this->catA->id,
            'member_id' => $this->memberA->id,
            'vet_id' => $this->doctor->id,
            'created_by' => $this->doctor->id,
            'service_type' => 'clinic',
            'status' => MedicalRecord::STATUS_COMPLETED,
            'chief_complaint' => 'Nafsu makan berkurang 2 hari',
            'weight' => 4.20,
            'temperature' => 38.6,
            'general_condition' => 'Aktif, hidrasi cukup baik',
            'recommendation' => 'Berikan pakan basah dan pantau nafsu makan.',
            'started_at' => now()->subHour(),
            'completed_at' => now(),
        ]);

        MedicalRecordDiagnosis::create([
            'medical_record_id' => $record->id,
            'diagnosis_name' => 'Gastritis Ringan',
            'is_primary' => true,
            'severity' => 'mild',
            'certainty' => 'confirmed',
        ]);

        $response = $this->actingAs($this->memberA)
            ->withSession(['active_role' => 'member'])
            ->get(route('member.medical-records.summary', $record));

        $response->assertStatus(200);
        $response->assertSee('Ringkasan Medis');
        $response->assertSee('Milo');
        $response->assertSee('Gastritis Ringan');
        $response->assertSee('4.20 kg');
        $response->assertSee('38.6');
        $response->assertSee('drh. Siti Rahma');
        $response->assertSee(route('member.medical-records.pdf', $record));
        $response->assertSee('window.print()', false);
    }

    public function test_member_cannot_view_draft_or_in_progress_medical_record()
    {
        $draftRecord = MedicalRecord::create([
            'record_number' => MedicalRecord::generateRecordNumber(),
            'cat_id' => $this->catA->id,
            'member_id' => $this->memberA->id,
            'vet_id' => $this->doctor->id,
            'created_by' => $this->doctor->id,
            'service_type' => 'clinic',
            'status' => MedicalRecord::STATUS_IN_PROGRESS,
            'chief_complaint' => 'Pemeriksaan rutin berjalan',
            'weight' => 4.00,
            'temperature' => 38.5,
            'general_condition' => 'Sedang diperiksa',
            'started_at' => now(),
        ]);

        $response = $this->actingAs($this->memberA)
            ->withSession(['active_role' => 'member'])
            ->get(route('member.medical-records.summary', $draftRecord));

        $response->assertStatus(302);
        $response->assertSessionHas('error', 'Rekam medis ini masih dalam proses pemeriksaan oleh dokter.');
    }

    public function test_member_cannot_view_medical_record_of_another_members_cat()
    {
        $recordB = MedicalRecord::create([
            'record_number' => MedicalRecord::generateRecordNumber(),
            'cat_id' => $this->catB->id,
            'member_id' => $this->memberB->id,
            'vet_id' => $this->doctor->id,
            'created_by' => $this->doctor->id,
            'service_type' => 'clinic',
            'status' => MedicalRecord::STATUS_COMPLETED,
            'chief_complaint' => 'Vaksinasi tahunan',
            'weight' => 3.50,
            'temperature' => 38.2,
            'general_condition' => 'Sehat dan aktif',
            'started_at' => now()->subHour(),
            'completed_at' => now(),
        ]);

        $response = $this->actingAs($this->memberA)
            ->withSession(['active_role' => 'member'])
            ->get(route('member.medical-records.summary', $recordB));

        $response->assertStatus(403);
    }

    public function test_member_can_stream_pdf_of_finalized_medical_record()
    {
        $record = MedicalRecord::create([
            'record_number' => MedicalRecord::generateRecordNumber(),
            'cat_id' => $this->catA->id,
            'member_id' => $this->memberA->id,
            'vet_id' => $this->doctor->id,
            'created_by' => $this->doctor->id,
            'service_type' => 'clinic',
            'status' => MedicalRecord::STATUS_COMPLETED,
            'chief_complaint' => 'Cek kesehatan',
            'weight' => 4.10,
            'temperature' => 38.3,
            'general_condition' => 'Sehat',
            'started_at' => now()->subHour(),
            'completed_at' => now(),
        ]);

        $response = $this->actingAs($this->memberA)
            ->withSession(['active_role' => 'member'])
            ->get(route('member.medical-records.pdf', $record));

        $response->assertStatus(200);
        $this->assertEquals('application/pdf', $response->headers->get('Content-Type'));
    }

    public function test_member_cannot_stream_pdf_of_draft_record()
    {
        $draftRecord = MedicalRecord::create([
            'record_number' => MedicalRecord::generateRecordNumber(),
            'cat_id' => $this->catA->id,
            'member_id' => $this->memberA->id,
            'vet_id' => $this->doctor->id,
            'created_by' => $this->doctor->id,
            'service_type' => 'clinic',
            'status' => MedicalRecord::STATUS_IN_PROGRESS,
            'chief_complaint' => 'Pemeriksaan berjalan',
            'weight' => 4.00,
            'temperature' => 38.5,
            'general_condition' => 'Sedang diperiksa',
            'started_at' => now(),
        ]);

        $response = $this->actingAs($this->memberA)
            ->withSession(['active_role' => 'member'])
            ->get(route('member.medical-records.pdf', $draftRecord));

        $response->assertStatus(403);
    }

    public function test_member_dashboard_displays_finalized_record_for_cat_with_pdf_link()
    {
        $record = MedicalRecord::create([
            'record_number' => MedicalRecord::generateRecordNumber(),
            'cat_id' => $this->catA->id,
            'member_id' => $this->memberA->id,
            'vet_id' => $this->doctor->id,
            'created_by' => $this->doctor->id,
            'service_type' => 'clinic',
            'status' => MedicalRecord::STATUS_COMPLETED,
            'chief_complaint' => 'Gatal di telinga',
            'weight' => 4.30,
            'temperature' => 38.4,
            'general_condition' => 'Ear mite infestasi ringan',
            'started_at' => now()->subHour(),
            'completed_at' => now(),
        ]);

        MedicalRecordDiagnosis::create([
            'medical_record_id' => $record->id,
            'diagnosis_name' => 'Otitis Externa Parasitik',
            'is_primary' => true,
            'severity' => 'mild',
            'certainty' => 'confirmed',
        ]);

        $response = $this->actingAs($this->memberA)
            ->withSession(['active_role' => 'member'])
            ->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Rekam Medis');
        $response->assertSee('Otitis Externa Parasitik');
        $response->assertSee(route('member.medical-records.summary', $record));
        $response->assertSee(route('member.medical-records.pdf', $record));
    }

    public function test_member_dashboard_appointments_table_displays_final_record_and_pdf_link()
    {
        $appointment = Appointment::create([
            'user_id' => $this->memberA->id,
            'cat_id' => $this->catA->id,
            'date' => now()->toDateString(),
            'time_slot' => '10:00 - 11:00',
            'status' => 'completed',
            'notes' => 'Pemeriksaan rutin berkala',
        ]);

        $record = MedicalRecord::create([
            'record_number' => MedicalRecord::generateRecordNumber(),
            'appointment_id' => $appointment->id,
            'cat_id' => $this->catA->id,
            'member_id' => $this->memberA->id,
            'vet_id' => $this->doctor->id,
            'created_by' => $this->doctor->id,
            'service_type' => 'clinic',
            'status' => MedicalRecord::STATUS_COMPLETED,
            'chief_complaint' => 'Keluhan',
            'weight' => 4.00,
            'temperature' => 38.2,
            'general_condition' => 'Sehat dan lincah',
            'started_at' => now()->subHour(),
            'completed_at' => now(),
        ]);

        MedicalRecordDiagnosis::create([
            'medical_record_id' => $record->id,
            'diagnosis_name' => 'Kucing Sehat (Medical Checkup)',
            'is_primary' => true,
            'severity' => 'mild',
            'certainty' => 'confirmed',
        ]);

        $response = $this->actingAs($this->memberA)
            ->withSession(['active_role' => 'member'])
            ->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Kucing Sehat (Medical Checkup)');
        $response->assertSee(route('member.medical-records.summary', $record));
        $response->assertSee(route('member.medical-records.pdf', $record));
    }
}
