<?php

namespace Tests\Feature;

use App\Models\Cat;
use App\Models\MedicalRecord;
use App\Models\Prescription;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PharmacyPrescriptionFilterTest extends TestCase
{
    use RefreshDatabase;

    protected User $farmasiUser;
    protected User $doctorUser;
    protected User $memberUser;
    protected Cat $cat;

    protected function setUp(): void
    {
        parent::setUp();

        $this->farmasiUser = User::factory()->create([
            'role' => 'farmasi',
            'roles' => ['farmasi', 'member'],
        ]);

        $this->doctorUser = User::factory()->create([
            'role' => 'dokter',
            'roles' => ['dokter', 'member'],
        ]);

        $this->memberUser = User::factory()->create([
            'role' => 'member',
            'roles' => ['member'],
        ]);

        $this->cat = Cat::create([
            'user_id' => $this->memberUser->id,
            'name' => 'Kimi',
            'breed' => 'Domestik',
            'gender' => 'male',
            'status' => 'alive',
            'date_of_birth' => '2024-01-01',
            'wilayah_code' => '34',
        ]);
    }

    public function test_farmasi_user_can_access_prescriptions_queue_with_default_today_filter()
    {
        // 1. Prescription created TODAY
        $recordToday = MedicalRecord::create([
            'record_number' => 'MR-TODAY-001',
            'cat_id' => $this->cat->id,
            'member_id' => $this->memberUser->id,
            'vet_id' => $this->doctorUser->id,
            'created_by' => $this->doctorUser->id,
            'service_type' => 'clinic',
            'status' => 'completed',
            'chief_complaint' => 'Kontrol hari ini',
            'weight' => 3.5,
            'temperature' => 38.5,
            'general_condition' => 'Active',
        ]);

        $rxToday = Prescription::create([
            'medical_record_id' => $recordToday->id,
            'prescribed_by' => $this->doctorUser->id,
            'prescription_number' => 'RX-TODAY-001',
            'status' => 'issued',
            'issued_at' => Carbon::today()->addHours(9),
        ]);

        // 2. Prescription created YESTERDAY (past day)
        $recordYesterday = MedicalRecord::create([
            'record_number' => 'MR-YESTERDAY-999',
            'cat_id' => $this->cat->id,
            'member_id' => $this->memberUser->id,
            'vet_id' => $this->doctorUser->id,
            'created_by' => $this->doctorUser->id,
            'service_type' => 'clinic',
            'status' => 'completed',
            'chief_complaint' => 'Kontrol kemarin',
            'weight' => 3.5,
            'temperature' => 38.5,
            'general_condition' => 'Active',
        ]);

        $rxYesterday = Prescription::create([
            'medical_record_id' => $recordYesterday->id,
            'prescribed_by' => $this->doctorUser->id,
            'prescription_number' => 'RX-YESTERDAY-999',
            'status' => 'issued',
            'issued_at' => Carbon::yesterday()->addHours(14),
        ]);

        // Access index page without query params (defaults to timeframe=today)
        $response = $this->actingAs($this->farmasiUser)->get(route('farmasi.prescriptions.index'));

        $response->assertStatus(200);
        $response->assertSee('Ruang Farmasi');
        $response->assertSee('Resep Hari Ini');
        $response->assertSee('Mode Fokus Aktif');
        $response->assertSee('RX-TODAY-001');
        // Yesterday's prescription must NOT be visible in today's focus mode
        $response->assertDontSee('RX-YESTERDAY-999');
    }

    public function test_farmasi_user_can_view_all_prescriptions_including_past_days_using_all_timeframe()
    {
        // Prescription from 3 days ago
        $recordPast = MedicalRecord::create([
            'record_number' => 'MR-PAST-777',
            'cat_id' => $this->cat->id,
            'member_id' => $this->memberUser->id,
            'vet_id' => $this->doctorUser->id,
            'created_by' => $this->doctorUser->id,
            'service_type' => 'clinic',
            'status' => 'completed',
            'chief_complaint' => 'Kontrol 3 hari lalu',
            'weight' => 3.5,
            'temperature' => 38.5,
            'general_condition' => 'Active',
        ]);

        $rxPast = Prescription::create([
            'medical_record_id' => $recordPast->id,
            'prescribed_by' => $this->doctorUser->id,
            'prescription_number' => 'RX-PAST-777',
            'status' => 'issued',
            'issued_at' => Carbon::now()->subDays(3),
        ]);

        // Today's prescription
        $recordToday = MedicalRecord::create([
            'record_number' => 'MR-TODAY-555',
            'cat_id' => $this->cat->id,
            'member_id' => $this->memberUser->id,
            'vet_id' => $this->doctorUser->id,
            'created_by' => $this->doctorUser->id,
            'service_type' => 'clinic',
            'status' => 'completed',
            'chief_complaint' => 'Kontrol hari ini',
            'weight' => 3.5,
            'temperature' => 38.5,
            'general_condition' => 'Active',
        ]);

        $rxToday = Prescription::create([
            'medical_record_id' => $recordToday->id,
            'prescribed_by' => $this->doctorUser->id,
            'prescription_number' => 'RX-TODAY-555',
            'status' => 'issued',
            'issued_at' => Carbon::today()->addHours(10),
        ]);

        // Access with timeframe=all
        $response = $this->actingAs($this->farmasiUser)
            ->get(route('farmasi.prescriptions.index', ['timeframe' => 'all']));

        $response->assertStatus(200);
        $response->assertSee('Semua Tanggal');
        $response->assertSee('RX-TODAY-555');
        $response->assertSee('RX-PAST-777');
    }

    public function test_farmasi_user_can_filter_by_status_within_today_timeframe()
    {
        // Today waiting prescription
        $recordWait = MedicalRecord::create([
            'record_number' => 'MR-WAIT-111',
            'cat_id' => $this->cat->id,
            'member_id' => $this->memberUser->id,
            'vet_id' => $this->doctorUser->id,
            'created_by' => $this->doctorUser->id,
            'service_type' => 'clinic',
            'status' => 'completed',
            'chief_complaint' => 'Keluhan batuk',
            'weight' => 3.5,
            'temperature' => 38.5,
            'general_condition' => 'Active',
        ]);

        $rxWait = Prescription::create([
            'medical_record_id' => $recordWait->id,
            'prescribed_by' => $this->doctorUser->id,
            'prescription_number' => 'RX-WAITING-111',
            'status' => 'issued',
            'issued_at' => Carbon::today()->addHours(8),
            'dispensed_at' => null,
        ]);

        // Today dispensed prescription
        $recordDisp = MedicalRecord::create([
            'record_number' => 'MR-DISP-222',
            'cat_id' => $this->cat->id,
            'member_id' => $this->memberUser->id,
            'vet_id' => $this->doctorUser->id,
            'created_by' => $this->doctorUser->id,
            'service_type' => 'clinic',
            'status' => 'completed',
            'chief_complaint' => 'Keluhan luka',
            'weight' => 3.5,
            'temperature' => 38.5,
            'general_condition' => 'Active',
        ]);

        $rxDisp = Prescription::create([
            'medical_record_id' => $recordDisp->id,
            'prescribed_by' => $this->doctorUser->id,
            'prescription_number' => 'RX-DISPENSED-222',
            'status' => 'dispensed',
            'issued_at' => Carbon::today()->addHours(9),
            'dispensed_at' => Carbon::today()->addHours(10),
            'dispensed_by' => $this->farmasiUser->id,
        ]);

        // 1. Status waiting + timeframe today
        $responseWaiting = $this->actingAs($this->farmasiUser)
            ->get(route('farmasi.prescriptions.index', ['status' => 'waiting', 'timeframe' => 'today']));

        $responseWaiting->assertStatus(200);
        $responseWaiting->assertSee('RX-WAITING-111');
        $responseWaiting->assertDontSee('RX-DISPENSED-222');

        // 2. Status dispensed + timeframe today
        $responseDispensed = $this->actingAs($this->farmasiUser)
            ->get(route('farmasi.prescriptions.index', ['status' => 'dispensed', 'timeframe' => 'today']));

        $responseDispensed->assertStatus(200);
        $responseDispensed->assertSee('RX-DISPENSED-222');
        $responseDispensed->assertDontSee('RX-WAITING-111');
    }
}
