<?php

namespace Tests\Feature;

use App\Models\RepresentativeRegistration;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class RepresentativeRegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_view_representative_registration_page()
    {
        $response = $this->get(route('representative.register'));
        $response->assertStatus(200);
        $response->assertSee('Penjaringan Representatif');
        $response->assertSee('Nomor Baku Muhammadiyah');
    }

    public function test_can_submit_representative_registration()
    {
        Storage::fake('public');

        $skPdf = UploadedFile::fake()->create('sk_pimpinan.pdf', 500, 'application/pdf');
        $ktamImg = UploadedFile::fake()->image('ktam_masa.jpg');

        $payload = [
            'name' => 'Ahmad Dahlan',
            'nbm' => '12345678',
            'birth_date' => '1995-08-17',
            'email' => 'ahmad.dahlan@example.com',
            'whatsapp_number' => '081234567890',
            'instagram_username' => 'ahmaddahlan_muh',
            'province_name' => 'D.I. Yogyakarta',
            'city_name' => 'Kota Yogyakarta',
            'district_name' => 'Kotagede',
            'village_name' => 'Rejowinangun',
            'latitude' => -7.801389,
            'longitude' => 110.364444,
            'formatted_address' => 'Jl. Gedongkuning, Rejowinangun, Kotagede, Yogyakarta',
            'muhammadiyah_active_leadership' => 'PRM Rejowinangun Kotagede',
            'sk_pimpinan_document' => $skPdf,
            'ktam_document' => $ktamImg,
            'animal_welfare_essay' => 'Kesejahteraan hewan dalam pandangan Islam dan persyarikatan Muhammadiyah adalah wujud rahmatan lil alamin. Program sterilisasi dan pendataan kucing sangat penting untuk menjaga kesehatan lingkungan.',
            'privacy_agreed' => '1',
        ];

        $response = $this->post(route('representative.store'), $payload);

        $this->assertDatabaseHas('representative_registrations', [
            'name' => 'Ahmad Dahlan',
            'nbm' => '12345678',
            'email' => 'ahmad.dahlan@example.com',
            'province_name' => 'D.I. Yogyakarta',
            'city_name' => 'Kota Yogyakarta',
            'status' => 'pending',
        ]);

        $reg = RepresentativeRegistration::where('email', 'ahmad.dahlan@example.com')->first();
        $this->assertNotNull($reg);
        $this->assertStringStartsWith('REP-', $reg->registration_number);

        $response->assertRedirect(route('representative.success', ['number' => $reg->registration_number]));
    }

    public function test_registration_validation_fails_when_required_fields_missing()
    {
        $response = $this->post(route('representative.store'), []);
        $response->assertSessionHasErrors([
            'name', 'nbm', 'birth_date', 'email', 'whatsapp_number',
            'province_name', 'city_name', 'district_name', 'village_name',
            'muhammadiyah_active_leadership', 'sk_pimpinan_document', 'ktam_document',
            'animal_welfare_essay', 'privacy_agreed'
        ]);
    }

    public function test_admin_can_view_and_process_representative_registration()
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $reg = RepresentativeRegistration::create([
            'registration_number' => 'REP-202610-TEST',
            'name' => 'Siti Walidah',
            'nbm' => '87654321',
            'birth_date' => '1998-05-12',
            'email' => 'siti.walidah@example.com',
            'whatsapp_number' => '08987654321',
            'province_name' => 'Jawa Tengah',
            'city_name' => 'Kota Surakarta (Solo)',
            'district_name' => 'Laweyan',
            'village_name' => 'Pajang',
            'muhammadiyah_active_leadership' => 'Pimpinan Cabang Nasyiatul Aisyiyah Laweyan',
            'sk_pimpinan_document_path' => 'representatives/sk_pimpinan/sample.pdf',
            'ktam_document_path' => 'representatives/ktam/sample.jpg',
            'animal_welfare_essay' => 'Komitmen edukasi kesrawan di lingkungan ranting dan cabang Aisyiyah.',
            'privacy_agreed' => true,
            'status' => 'pending',
        ]);

        // Admin index with coordinates
        $regWithGeo = RepresentativeRegistration::create([
            'registration_number' => 'REP-202610-GEO1',
            'name' => 'K.H. Mas Mansur',
            'nbm' => '11223344',
            'birth_date' => '1990-01-01',
            'email' => 'mas.mansur@example.com',
            'whatsapp_number' => '081122334455',
            'province_name' => 'Jawa Timur',
            'city_name' => 'Kota Surabaya',
            'district_name' => 'Genteng',
            'village_name' => 'Embong Kaliasin',
            'latitude' => -7.2625,
            'longitude' => 112.7483,
            'formatted_address' => 'Jl. Embong Kaliasin, Genteng, Surabaya',
            'muhammadiyah_active_leadership' => 'PWM Jawa Timur',
            'sk_pimpinan_document_path' => 'representatives/sk_pimpinan/sample.pdf',
            'ktam_document_path' => 'representatives/ktam/sample.jpg',
            'animal_welfare_essay' => 'Uraian kesrawan tingkat wilayah Jawa Timur.',
            'privacy_agreed' => true,
            'status' => 'approved',
        ]);

        // Admin index
        $indexResponse = $this->actingAs($admin)->get(route('admin.representatives.index'));
        $indexResponse->assertStatus(200);
        $indexResponse->assertSee('Siti Walidah');
        $indexResponse->assertSee('K.H. Mas Mansur');
        $indexResponse->assertSee('Peta Sebaran Representatif Wilayah');
        $indexResponse->assertSee('representatives-map');
        $indexResponse->assertViewHas('mapRepresentatives');
        $indexResponse->assertViewHas('totalMapped', 1);
        $indexResponse->assertViewHas('stats');

        // Admin show
        $showResponse = $this->actingAs($admin)->get(route('admin.representatives.show', $reg));
        $showResponse->assertStatus(200);
        $showResponse->assertSee('87654321');

        // Admin update status to approved
        $updateResponse = $this->actingAs($admin)->put(route('admin.representatives.update-status', $reg), [
            'status' => 'approved',
            'admin_notes' => 'Dokumen SK dan NBM telah diverifikasi valid.',
        ]);

        $updateResponse->assertRedirect();
        $this->assertDatabaseHas('representative_registrations', [
            'id' => $reg->id,
            'status' => 'approved',
            'admin_notes' => 'Dokumen SK dan NBM telah diverifikasi valid.',
            'reviewed_by' => $admin->id,
        ]);

        // Admin export CSV
        $exportResponse = $this->actingAs($admin)->get(route('admin.representatives.export'));
        $exportResponse->assertStatus(200);
        $this->assertStringContainsString('text/csv', $exportResponse->headers->get('Content-Type'));
    }
}
