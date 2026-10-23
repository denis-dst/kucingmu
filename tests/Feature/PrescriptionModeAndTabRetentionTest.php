<?php

namespace Tests\Feature;

use App\Models\Cat;
use App\Models\Event;
use App\Models\KtamCard;
use App\Models\MedicalRecord;
use App\Models\Prescription;
use App\Models\PrescriptionItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PrescriptionModeAndTabRetentionTest extends TestCase
{
    use RefreshDatabase;

    private User $doctor;
    private User $member;
    private Cat $cat;
    private MedicalRecord $record;

    protected function setUp(): void
    {
        parent::setUp();

        $this->doctor = User::factory()->create([
            'role' => 'dokter',
            'roles' => ['dokter'],
        ]);

        $this->member = User::factory()->create([
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
            'unique_code' => '34.kcg.0099',
        ]);

        KtamCard::create([
            'cat_id' => $this->cat->id,
            'ktam_number' => '34.kcg.0099',
            'verified_by' => $this->doctor->id,
            'verified_at' => now(),
            'issue_date' => now(),
            'status' => 'issued',
            'qr_code_payload' => 'payload_test_99',
        ]);

        $this->record = MedicalRecord::create([
            'record_number' => 'MR-202610-0099',
            'cat_id' => $this->cat->id,
            'member_id' => $this->member->id,
            'vet_id' => $this->doctor->id,
            'created_by' => $this->doctor->id,
            'service_type' => 'clinic',
            'status' => 'in_progress',
            'chief_complaint' => 'Batuk dan pilek sejak 2 hari lalu',
            'weight' => 3.5,
            'temperature' => 38.6,
            'general_condition' => 'Active',
        ]);

        $this->record->diagnoses()->create([
            'diagnosis_name' => 'Feline Upper Respiratory Tract Infection (URTI)',
            'diagnosis_type' => 'primary',
            'certainty' => 'provisional',
            'severity' => 'mild',
            'is_primary' => true,
        ]);
    }

    public function test_adding_regular_prescription_item_redirects_to_step_4(): void
    {
        $response = $this->actingAs($this->doctor)->post(
            route('dokter.medical-records.prescription.add', $this->record),
            [
                'is_racikan' => '0',
                'medicine_name' => 'Clavamox Drops 15ml',
                'dosage_form' => 'sirup',
                'dose_value' => 0.5,
                'dose_unit' => 'ml',
                'dose_basis' => 'fixed',
                'administration_route' => 'oral',
                'frequency_value' => '2x sehari',
                'duration_value' => '7 hari',
                'quantity_value' => 1,
                'quantity_unit' => 'botol',
                'usage_instructions' => 'Kocok dahulu, berikan sesudah makan',
                'warnings' => 'Simpan di kulkas setelah dibuka',
            ]
        );

        $response->assertRedirect(route('dokter.medical-records.examine', [
            'record' => $this->record->id,
            'step' => 4,
        ]));

        $this->assertDatabaseHas('prescription_items', [
            'medicine_name_snapshot' => 'Clavamox Drops 15ml',
            'is_racikan' => false,
            'dosage_form' => 'sirup',
        ]);

        $prescription = $this->record->prescriptions()->first();
        $this->assertNotNull($prescription);
        $this->assertEquals(Prescription::TYPE_REGULAR, $prescription->prescription_type);
    }

    public function test_adding_racikan_prescription_item_saves_compounding_data_and_redirects_to_step_4(): void
    {
        $response = $this->actingAs($this->doctor)->post(
            route('dokter.medical-records.prescription.add', $this->record),
            [
                'is_racikan' => '1',
                'racikan_name' => 'Puyer Flu & Batuk Pasien',
                'composition' => 'Amoxicillin 50 mg + Dexamethasone 0.25 mg + Bromhexine 1 mg (per bungkus)',
                'dosage_form' => 'puyer',
                'quantity_value' => 10,
                'quantity_unit' => 'bungkus',
                'administration_route' => 'oral',
                'frequency_value' => '2x sehari',
                'duration_value' => '5 hari',
                'usage_instructions' => 'Campur 1 bungkus ke sedikit wet food sesudah makan',
                'warnings' => 'Habiskan seluruh bungkus racikan',
            ]
        );

        $response->assertRedirect(route('dokter.medical-records.examine', [
            'record' => $this->record->id,
            'step' => 4,
        ]));

        $this->assertDatabaseHas('prescription_items', [
            'is_racikan' => true,
            'racikan_name' => 'Puyer Flu & Batuk Pasien',
            'composition' => 'Amoxicillin 50 mg + Dexamethasone 0.25 mg + Bromhexine 1 mg (per bungkus)',
            'dosage_form' => 'puyer',
            'quantity_value' => 10,
            'quantity_unit' => 'bungkus',
        ]);

        $prescription = $this->record->prescriptions()->first();
        $this->assertNotNull($prescription);
        $this->assertEquals(Prescription::TYPE_COMPOUNDED, $prescription->prescription_type);
    }

    public function test_setting_prescription_mode_to_manual_redirects_to_step_4(): void
    {
        $response = $this->actingAs($this->doctor)->post(
            route('dokter.medical-records.prescription.mode', $this->record),
            [
                'prescription_type' => 'manual',
                'notes' => 'Resep manual ditulis di blanko klinik No. 088 untuk ditebus di apotek luar.',
            ]
        );

        $response->assertRedirect(route('dokter.medical-records.examine', [
            'record' => $this->record->id,
            'step' => 4,
        ]));

        $prescription = $this->record->prescriptions()->first();
        $this->assertNotNull($prescription);
        $this->assertTrue($prescription->isManual());
        $this->assertEquals('Resep manual ditulis di blanko klinik No. 088 untuk ditebus di apotek luar.', $prescription->notes);
    }

    public function test_finalizing_manual_prescription_marks_status_as_issued(): void
    {
        $this->actingAs($this->doctor)->post(
            route('dokter.medical-records.prescription.mode', $this->record),
            [
                'prescription_type' => 'manual',
                'notes' => 'Blanko Fisik 088',
            ]
        );

        $finalizeResponse = $this->actingAs($this->doctor)->post(
            route('dokter.medical-records.finalize', $this->record)
        );

        $finalizeResponse->assertRedirect(route('dokter.medical-records.show', $this->record));

        $prescription = $this->record->fresh()->prescriptions()->first();
        $this->assertNotNull($prescription);
        $this->assertEquals('issued', $prescription->status);
        $this->assertTrue($prescription->isManual());
    }

    public function test_print_sheet_displays_resep_manual_label_when_in_manual_mode(): void
    {
        $prescription = Prescription::create([
            'medical_record_id' => $this->record->id,
            'prescribed_by' => $this->doctor->id,
            'prescription_number' => Prescription::generatePrescriptionNumber(),
            'prescription_type' => Prescription::TYPE_MANUAL,
            'status' => 'issued',
            'issued_at' => now(),
            'notes' => 'Ditebus di Apotek Kimia Farma dengan blanko fisik',
        ]);

        $response = $this->actingAs($this->doctor)->get(
            route('dokter.medical-records.print', $this->record)
        );

        $response->assertOk();
        $response->assertSee('Resep Manual');
        $response->assertSee('Ditebus di Apotek Kimia Farma dengan blanko fisik');
    }

    public function test_print_sheet_displays_racikan_badge_and_composition_when_compounded(): void
    {
        $prescription = Prescription::create([
            'medical_record_id' => $this->record->id,
            'prescribed_by' => $this->doctor->id,
            'prescription_number' => Prescription::generatePrescriptionNumber(),
            'prescription_type' => Prescription::TYPE_COMPOUNDED,
            'status' => 'issued',
            'issued_at' => now(),
        ]);

        PrescriptionItem::create([
            'prescription_id' => $prescription->id,
            'medicine_name_snapshot' => 'Puyer Flu & Batuk Pasien',
            'is_racikan' => true,
            'racikan_name' => 'Puyer Flu & Batuk Pasien',
            'composition' => 'Amoxicillin 50 mg + Dexamethasone 0.25 mg',
            'dosage_form' => 'puyer',
            'dose_value' => 1,
            'dose_unit' => 'bungkus',
            'dose_basis' => 'fixed',
            'administration_route' => 'oral',
            'frequency_value' => '2x sehari',
            'duration_value' => '5 hari',
            'quantity_value' => 10,
            'quantity_unit' => 'bungkus',
            'usage_instructions' => 'Sesudah makan',
        ]);

        $response = $this->actingAs($this->doctor)->get(
            route('dokter.medical-records.print', $this->record)
        );

        $response->assertOk();
        $response->assertSee('Racikan');
        $response->assertSee('Puyer Flu & Batuk Pasien');
        $response->assertSee('Amoxicillin 50 mg + Dexamethasone 0.25 mg');
    }

    public function test_deleting_prescription_item_redirects_to_step_4(): void
    {
        $prescription = Prescription::create([
            'medical_record_id' => $this->record->id,
            'prescribed_by' => $this->doctor->id,
            'prescription_number' => Prescription::generatePrescriptionNumber(),
            'prescription_type' => Prescription::TYPE_REGULAR,
            'status' => 'draft',
        ]);

        $item = PrescriptionItem::create([
            'prescription_id' => $prescription->id,
            'medicine_name_snapshot' => 'Doxycat 50mg',
            'is_racikan' => false,
            'dosage_form' => 'tablet',
            'dose_value' => 1,
            'dose_unit' => 'tablet',
            'dose_basis' => 'fixed',
            'administration_route' => 'oral',
            'frequency_value' => '1x sehari',
            'duration_value' => '7 hari',
            'quantity_value' => 7,
            'quantity_unit' => 'tablet',
            'usage_instructions' => 'Setelah makan',
        ]);

        $response = $this->actingAs($this->doctor)->delete(
            route('dokter.medical-records.prescription.delete', $item)
        );

        $response->assertRedirect(route('dokter.medical-records.examine', [
            'record' => $this->record->id,
            'step' => 4,
        ]));

        $this->assertDatabaseMissing('prescription_items', ['id' => $item->id]);
    }

    public function test_medical_record_create_page_renders_event_facilities_in_select_list(): void
    {
        $event = Event::create([
            'title' => 'Baksos Vaksinasi Kotagede',
            'description' => 'Vaksinasi massal',
            'date' => now()->addDays(2),
            'location' => 'Klinik PKU Muhammadiyah Kotagede',
            'status' => 'active',
        ]);

        $response = $this->actingAs($this->doctor)->get(
            route('dokter.medical-records.create')
        );

        $response->assertOk();
        $response->assertSee('Klinik PKU Muhammadiyah Kotagede');
        $response->assertSee('Baksos Vaksinasi Kotagede');
        $response->assertSee('List Event');
        $response->assertSee('Ketik Manual');
    }

    public function test_doctor_can_store_record_with_facility_from_event(): void
    {
        $event = Event::create([
            'title' => 'Baksos Steril Kucing',
            'description' => 'Steril kucing',
            'date' => now()->addDays(5),
            'location' => 'RSH Prof Soeparwi UGM',
            'status' => 'active',
        ]);

        $response = $this->actingAs($this->doctor)->post(
            route('dokter.medical-records.store'),
            [
                'cat_id' => $this->cat->id,
                'service_type' => 'clinic',
                'clinic_name' => $event->location,
                'chief_complaint' => 'Pemeriksaan rutin saat baksos',
            ]
        );

        $record = MedicalRecord::where('cat_id', $this->cat->id)->latest()->first();
        $this->assertNotNull($record);
        $this->assertEquals('RSH Prof Soeparwi UGM', $record->clinic_name);
        $response->assertRedirect(route('dokter.medical-records.examine', $record));
    }

    public function test_doctor_can_store_record_with_manual_custom_facility_outside_events(): void
    {
        $response = $this->actingAs($this->doctor)->post(
            route('dokter.medical-records.store'),
            [
                'cat_id' => $this->cat->id,
                'service_type' => 'clinic',
                'clinic_name' => 'Klinik Hewan Mandiri drh. Rahmat',
                'chief_complaint' => 'Kucing pilek',
            ]
        );

        $record = MedicalRecord::where('cat_id', $this->cat->id)->latest()->first();
        $this->assertNotNull($record);
        $this->assertEquals('Klinik Hewan Mandiri drh. Rahmat', $record->clinic_name);
        $response->assertRedirect(route('dokter.medical-records.examine', $record));
    }
}

