<?php

namespace Tests\Feature;

use App\Models\Cat;
use App\Models\KtamCard;
use App\Models\MedicalRecord;
use App\Models\Prescription;
use App\Models\PrescriptionItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PrescriptionCopyTest extends TestCase
{
    use RefreshDatabase;

    private User $doctor;
    private User $member;
    private Cat $targetCat;
    private MedicalRecord $targetRecord;

    private Cat $sourceCat;
    private MedicalRecord $sourceRecord;
    private Prescription $sourceRegularPrescription;
    private Prescription $sourceCompoundedPrescription;

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

        // Target Cat & Record
        $this->targetCat = Cat::create([
            'user_id' => $this->member->id,
            'name' => 'Mimi',
            'breed' => 'Persia',
            'gender' => 'female',
            'status' => 'alive',
            'date_of_birth' => '2023-05-10',
            'wilayah_code' => '34',
            'unique_code' => '34.kcg.0101',
        ]);

        KtamCard::create([
            'cat_id' => $this->targetCat->id,
            'ktam_number' => '34.kcg.0101',
            'verified_by' => $this->doctor->id,
            'verified_at' => now(),
            'issue_date' => now(),
            'status' => 'issued',
            'qr_code_payload' => 'payload_target_101',
        ]);

        $this->targetRecord = MedicalRecord::create([
            'record_number' => 'MR-202610-0101',
            'cat_id' => $this->targetCat->id,
            'member_id' => $this->member->id,
            'vet_id' => $this->doctor->id,
            'created_by' => $this->doctor->id,
            'service_type' => 'clinic',
            'status' => 'in_progress',
            'chief_complaint' => 'Flu dan bersin',
            'weight' => 3.2,
            'temperature' => 38.5,
            'general_condition' => 'Active',
        ]);

        $this->targetRecord->diagnoses()->create([
            'diagnosis_name' => 'Rhinitis Akut',
            'diagnosis_type' => 'primary',
            'certainty' => 'provisional',
            'severity' => 'mild',
            'is_primary' => true,
        ]);

        // Source Cat & Record (Another Cat)
        $this->sourceCat = Cat::create([
            'user_id' => $this->member->id,
            'name' => 'Kuro',
            'breed' => 'Domestik',
            'gender' => 'male',
            'status' => 'alive',
            'date_of_birth' => '2022-01-01',
            'wilayah_code' => '34',
            'unique_code' => '34.kcg.0102',
        ]);

        KtamCard::create([
            'cat_id' => $this->sourceCat->id,
            'ktam_number' => '34.kcg.0102',
            'verified_by' => $this->doctor->id,
            'verified_at' => now(),
            'issue_date' => now(),
            'status' => 'issued',
            'qr_code_payload' => 'payload_source_102',
        ]);

        $this->sourceRecord = MedicalRecord::create([
            'record_number' => 'MR-202610-0102',
            'cat_id' => $this->sourceCat->id,
            'member_id' => $this->member->id,
            'vet_id' => $this->doctor->id,
            'created_by' => $this->doctor->id,
            'service_type' => 'clinic',
            'status' => 'completed',
            'chief_complaint' => 'ISPA dan Batuk',
            'weight' => 4.0,
            'temperature' => 39.0,
            'general_condition' => 'Active',
        ]);

        $this->sourceRecord->diagnoses()->create([
            'diagnosis_name' => 'Feline URTI Complicated',
            'diagnosis_type' => 'primary',
            'certainty' => 'confirmed',
            'severity' => 'moderate',
            'is_primary' => true,
        ]);

        // Source Regular Prescription
        $this->sourceRegularPrescription = Prescription::create([
            'prescription_number' => 'RX-202610-0102',
            'medical_record_id' => $this->sourceRecord->id,
            'prescribed_by' => $this->doctor->id,
            'prescription_type' => Prescription::TYPE_REGULAR,
            'status' => 'issued',
        ]);

        PrescriptionItem::create([
            'prescription_id' => $this->sourceRegularPrescription->id,
            'medicine_name_snapshot' => 'Amoxicillin Trihydrate 100mg',
            'is_racikan' => false,
            'dosage_form' => 'tablet',
            'dose_value' => 1.0,
            'dose_unit' => 'tablet',
            'dose_basis' => 'fixed',
            'administration_route' => 'oral',
            'frequency_value' => '2x sehari',
            'frequency_unit' => 'kali sehari',
            'duration_value' => '5 hari',
            'duration_unit' => 'hari',
            'quantity_value' => 10,
            'quantity_unit' => 'tablet',
            'usage_instructions' => 'Berikan setelah makan pagi dan sore',
            'warnings' => 'Habiskan seluruh antibiotik',
        ]);

        // Source Compounded Prescription
        $this->sourceCompoundedPrescription = Prescription::create([
            'prescription_number' => 'RX-202610-0103',
            'medical_record_id' => $this->sourceRecord->id,
            'prescribed_by' => $this->doctor->id,
            'prescription_type' => Prescription::TYPE_COMPOUNDED,
            'status' => 'issued',
        ]);

        PrescriptionItem::create([
            'prescription_id' => $this->sourceCompoundedPrescription->id,
            'medicine_name_snapshot' => 'Puyer ISPA Khusus Kucing',
            'is_racikan' => true,
            'racikan_name' => 'Puyer ISPA Khusus Kucing',
            'composition' => 'Doxycycline 25mg + Dexamethasone 0.25mg + Salbutamol 0.5mg',
            'dosage_form' => 'puyer',
            'dose_value' => 1.0,
            'dose_unit' => 'bungkus',
            'dose_basis' => 'fixed',
            'administration_route' => 'oral',
            'frequency_value' => '2x sehari',
            'frequency_unit' => 'kali sehari',
            'duration_value' => '7 hari',
            'duration_unit' => 'hari',
            'quantity_value' => 14,
            'quantity_unit' => 'bungkus',
            'usage_instructions' => 'Campurkan dengan sedikit makanan basah atau madu',
            'warnings' => 'Simpan di tempat kering dan sejuk',
        ]);
    }

    public function test_examine_page_step_4_displays_copy_resep_buttons_and_modal(): void
    {
        $response = $this->actingAs($this->doctor)->get(
            route('dokter.medical-records.examine', ['record' => $this->targetRecord->id, 'step' => 4])
        );

        $response->assertStatus(200);
        $response->assertSee('Salin dari Resep Lain (Copy Resep)');
        $response->assertSee('Salin Formula Racikan (Copy Resep)');
        $response->assertSee('Salin Resep Pasien Lain (Copy Resep)');
        $response->assertSee('Kuro');
        $response->assertSee('Puyer ISPA Khusus Kucing');
    }

    public function test_doctor_can_copy_entire_regular_prescription_from_another_cat(): void
    {
        $response = $this->actingAs($this->doctor)->post(
            route('dokter.medical-records.prescription.copy', $this->targetRecord),
            [
                'prescription_id' => $this->sourceRegularPrescription->id,
            ]
        );

        $response->assertRedirect(route('dokter.medical-records.examine', ['record' => $this->targetRecord->id, 'step' => 4]));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('prescriptions', [
            'medical_record_id' => $this->targetRecord->id,
            'status' => 'draft',
        ]);

        $targetDraftRx = Prescription::where('medical_record_id', $this->targetRecord->id)->first();
        $this->assertNotNull($targetDraftRx);

        $this->assertDatabaseHas('prescription_items', [
            'prescription_id' => $targetDraftRx->id,
            'medicine_name_snapshot' => 'Amoxicillin Trihydrate 100mg',
            'is_racikan' => false,
            'dosage_form' => 'tablet',
            'frequency_value' => '2x sehari',
        ]);

        $this->targetRecord->refresh();
        $this->assertEquals(4, $this->targetRecord->last_active_step);
    }

    public function test_doctor_can_copy_entire_compounded_prescription_from_another_cat(): void
    {
        $response = $this->actingAs($this->doctor)->post(
            route('dokter.medical-records.prescription.copy', $this->targetRecord),
            [
                'prescription_id' => $this->sourceCompoundedPrescription->id,
            ]
        );

        $response->assertRedirect(route('dokter.medical-records.examine', ['record' => $this->targetRecord->id, 'step' => 4]));
        $response->assertSessionHas('success');

        $targetDraftRx = Prescription::where('medical_record_id', $this->targetRecord->id)->first();
        $this->assertNotNull($targetDraftRx);
        $this->assertEquals(Prescription::TYPE_COMPOUNDED, $targetDraftRx->prescription_type);

        $this->assertDatabaseHas('prescription_items', [
            'prescription_id' => $targetDraftRx->id,
            'is_racikan' => true,
            'racikan_name' => 'Puyer ISPA Khusus Kucing',
            'composition' => 'Doxycycline 25mg + Dexamethasone 0.25mg + Salbutamol 0.5mg',
            'dosage_form' => 'puyer',
            'quantity_value' => 14,
        ]);
    }

    public function test_doctor_can_copy_single_item_from_another_prescription(): void
    {
        $compoundedItem = $this->sourceCompoundedPrescription->items()->first();
        $this->assertNotNull($compoundedItem);

        $response = $this->actingAs($this->doctor)->post(
            route('dokter.medical-records.prescription.copy', $this->targetRecord),
            [
                'prescription_item_id' => $compoundedItem->id,
            ]
        );

        $response->assertRedirect(route('dokter.medical-records.examine', ['record' => $this->targetRecord->id, 'step' => 4]));
        $response->assertSessionHas('success');

        $targetDraftRx = Prescription::where('medical_record_id', $this->targetRecord->id)->first();
        $this->assertNotNull($targetDraftRx);

        $this->assertEquals(1, $targetDraftRx->items()->count());
        $copiedItem = $targetDraftRx->items()->first();
        $this->assertEquals('Puyer ISPA Khusus Kucing', $copiedItem->racikan_name);
        $this->assertEquals('Doxycycline 25mg + Dexamethasone 0.25mg + Salbutamol 0.5mg', $copiedItem->composition);
    }

    public function test_copy_prescription_validation_requires_prescription_or_item_id(): void
    {
        $response = $this->actingAs($this->doctor)->post(
            route('dokter.medical-records.prescription.copy', $this->targetRecord),
            []
        );

        $response->assertRedirect(route('dokter.medical-records.examine', ['record' => $this->targetRecord->id, 'step' => 4]));
        $response->assertSessionHas('error', 'Pilih resep atau item resep yang ingin disalin.');
    }

    public function test_member_cannot_copy_prescription(): void
    {
        $response = $this->actingAs($this->member)->post(
            route('dokter.medical-records.prescription.copy', $this->targetRecord),
            [
                'prescription_id' => $this->sourceRegularPrescription->id,
            ]
        );

        $response->assertStatus(403);
    }
}
