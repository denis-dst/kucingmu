<?php

namespace Tests\Feature;

use App\Models\Cat;
use App\Models\PetaAnabul;
use App\Models\TemuAnabul;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PetaAndTemuAnabulTest extends TestCase
{
    use RefreshDatabase;

    private User $member;
    private User $otherMember;

    protected function setUp(): void
    {
        parent::setUp();

        $this->member = User::factory()->create([
            'name' => 'Budi Santoso',
            'email' => 'budi@example.com',
            'role' => 'member',
            'roles' => ['member'],
            'phone' => '08123456789',
        ]);

        $this->otherMember = User::factory()->create([
            'name' => 'Siti Rahma',
            'email' => 'siti@example.com',
            'role' => 'member',
            'roles' => ['member'],
            'phone' => '08987654321',
        ]);
    }

    /**
     * Requirement: Untuk view bisa tanpa login (Akses Publik).
     */
    public function test_guest_can_view_all_public_views_without_login()
    {
        // 1. Peta Anabul: Index & Markers
        $this->get(route('peta-anabul.index'))->assertStatus(200);
        $this->getJson(route('peta-anabul.markers'))->assertStatus(200)->assertJsonStructure(['status', 'count', 'data']);

        // 2. Peta Anabul: Show Detail
        $peta = PetaAnabul::create([
            'user_id' => $this->member->id,
            'ownership_type' => 'stray',
            'name' => 'Kucing Malioboro',
            'unique_code' => PetaAnabul::generateUniqueCode(),
            'gender' => 'jantan',
            'condition' => 'sehat',
            'sterilization_status' => 'belum',
            'latitude' => -7.7928,
            'longitude' => 110.3658,
            'status' => 'aktif',
        ]);
        $this->get(route('peta-anabul.show', $peta->id))->assertStatus(200);

        // 3. Temu Anabul: Index, Map, Markers
        $this->get(route('temu-anabul.index'))->assertStatus(200);
        $this->get(route('temu-anabul.map'))->assertStatus(200);
        $this->getJson(route('temu-anabul.markers'))->assertStatus(200)->assertJsonStructure(['status', 'count', 'data']);

        // 4. Temu Anabul: Show Detail & Poster
        $temu = TemuAnabul::create([
            'user_id' => $this->member->id,
            'type' => 'lost',
            'title' => 'Dicari Kucing Hilang',
            'cat_name' => 'Milo',
            'gender' => 'jantan',
            'distinctive_features' => 'Kalung merah',
            'condition' => 'sehat',
            'incident_date' => now(),
            'latitude' => -7.7800,
            'longitude' => 110.3600,
            'contact_name' => 'Budi',
            'contact_phone' => '08123456789',
        ]);
        $this->get(route('temu-anabul.show', $temu->id))->assertStatus(200);
        $this->get(route('temu-anabul.poster', $temu->id))->assertStatus(200);
    }

    /**
     * Requirement: Visualisasi Peta Sebaran Representatif KucingMu yg sudah disetujui serta Peta Anabul dengan perbedaan Tab di homepage.
     */
    public function test_welcome_page_renders_peta_sebaran_section_and_tabs()
    {
        // 1. Create an approved representative
        \App\Models\RepresentativeRegistration::create([
            'name' => 'H. Agus Prasetyo, M.Si',
            'nbm' => '982341',
            'birth_date' => '1985-04-12',
            'email' => 'agus.prasetyo@example.com',
            'whatsapp_number' => '081234567890',
            'province_name' => 'DI Yogyakarta',
            'city_name' => 'Kota Yogyakarta',
            'district_name' => 'Gondomanan',
            'village_name' => 'Prawirodirjan',
            'latitude' => -7.8014,
            'longitude' => 110.3647,
            'formatted_address' => 'Jl. Kauman No. 23, Prawirodirjan, Gondomanan, Kota Yogyakarta',
            'muhammadiyah_active_leadership' => 'Pimpinan Daerah Muhammadiyah (PDM) Kota Yogyakarta',
            'sk_pimpinan_document_path' => 'documents/sk_sample.pdf',
            'ktam_document_path' => 'documents/ktam_sample.jpg',
            'animal_welfare_essay' => 'Komitmen pemberdayaan kesrawan dan edukasi sterilisasi kucing di lingkungan persyarikatan.',
            'status' => 'approved',
            'privacy_agreed' => true,
        ]);

        // 2. Visit homepage (GET /)
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('id="peta-sebaran"', false);
        $response->assertSee('Peta Sebaran Representatif & Anabul KucingMu', false);
        $response->assertSee('Peta Representatif Disetujui', false);
        $response->assertSee('Peta Anabul', false);
        $response->assertSee('welcome-community-map', false);

        // 3. Check approved representative markers API
        $repMarkersResponse = $this->getJson(route('api.representatives.markers'));
        $repMarkersResponse->assertStatus(200)
            ->assertJsonStructure(['status', 'count', 'data'])
            ->assertJsonFragment(['name' => 'H. Agus Prasetyo, M.Si']);
    }

    /**
     * Requirement: Untuk menambahkan, harus login sebagai member.
     */
    public function test_guest_cannot_add_and_is_redirected_to_login()
    {
        $peta = PetaAnabul::create([
            'user_id' => $this->member->id,
            'ownership_type' => 'stray',
            'name' => 'Kucing Kampus',
            'unique_code' => PetaAnabul::generateUniqueCode(),
            'gender' => 'jantan',
            'condition' => 'sehat',
            'sterilization_status' => 'belum',
            'latitude' => -7.7928,
            'longitude' => 110.3658,
        ]);

        $temu = TemuAnabul::create([
            'user_id' => $this->member->id,
            'type' => 'lost',
            'title' => 'Kucing Lepas',
            'cat_name' => 'Luna',
            'gender' => 'betina',
            'distinctive_features' => 'Mata biru',
            'condition' => 'sehat',
            'incident_date' => now(),
            'latitude' => -7.7800,
            'longitude' => 110.3600,
            'contact_name' => 'Budi',
            'contact_phone' => '08123456789',
        ]);

        // Peta Anabul: Create page & Store submission
        $this->get(route('peta-anabul.create'))->assertRedirect(route('login'));
        $this->post(route('peta-anabul.store'), ['name' => 'Test'])->assertRedirect(route('login'));
        $this->post(route('peta-anabul.update-info', $peta->id), ['notes' => 'Test'])->assertRedirect(route('login'));

        // Temu Anabul: Create page, Store submission, Sighting, & Reunited
        $this->get(route('temu-anabul.create'))->assertRedirect(route('login'));
        $this->post(route('temu-anabul.store'), ['cat_name' => 'Test'])->assertRedirect(route('login'));
        $this->post(route('temu-anabul.sighting', $temu->id), ['notes' => 'Test'])->assertRedirect(route('login'));
        $this->post(route('temu-anabul.reunited', $temu->id), ['reunion_notes' => 'Test'])->assertRedirect(route('login'));
    }

    /**
     * Requirement: Member terautentikasi dapat mengakses form tambah dan menambahkan data.
     */
    public function test_member_can_access_create_forms()
    {
        $this->actingAs($this->member)->get(route('peta-anabul.create'))->assertStatus(200);
        $this->actingAs($this->member)->get(route('temu-anabul.create'))->assertStatus(200);
    }

    public function test_member_can_create_stray_cat_and_pet_cat_in_peta_anabul()
    {
        // 1. Create Stray Cat (Kucing Liar Sekitar)
        $strayData = [
            'ownership_type' => 'stray',
            'name' => 'Si Belang Pasar',
            'gender' => 'jantan',
            'coat_color' => 'Oranye Putih',
            'coat_pattern' => 'Tabby',
            'condition' => 'sehat',
            'sterilization_status' => 'belum',
            'latitude' => -7.7956,
            'longitude' => 110.3695,
            'city' => 'Kota Yogyakarta',
            'district' => 'Gondomanan',
            'characteristics' => 'Ekor bengkok di ujung, telinga utuh',
        ];

        $responseStray = $this->actingAs($this->member)->post(route('peta-anabul.store'), $strayData);
        $responseStray->assertRedirect();

        $this->assertDatabaseHas('peta_anabuls', [
            'name' => 'Si Belang Pasar',
            'ownership_type' => 'stray',
            'user_id' => $this->member->id,
        ]);

        // 2. Create Member Pet Cat (Kucing Peliharaan)
        $petCat = Cat::create([
            'user_id' => $this->member->id,
            'name' => 'Mimi',
            'breed' => 'Persia Mix',
            'gender' => 'female',
            'status' => 'alive',
            'color' => 'Abu-abu',
            'date_of_birth' => '2023-01-01',
        ]);

        // 2. Create Member Pet Cat with ONLY cat_id and GIS coordinates (no manual identity)
        $petData = [
            'ownership_type' => 'pet',
            'cat_id' => $petCat->id,
            'latitude' => -7.7900,
            'longitude' => 110.3600,
            'city' => 'Kota Yogyakarta',
        ];

        $responsePet = $this->actingAs($this->member)->post(route('peta-anabul.store'), $petData);
        $responsePet->assertRedirect();

        $this->assertDatabaseHas('peta_anabuls', [
            'name' => 'Mimi',
            'gender' => 'betina',
            'coat_color' => 'Abu-abu',
            'ownership_type' => 'pet',
            'cat_id' => $petCat->id,
            'condition' => 'sehat',
        ]);

        // 3. Verify Markers API visual pin distinction
        $markersResponse = $this->getJson(route('peta-anabul.markers'));
        $markersResponse->assertStatus(200);

        $data = $markersResponse->json('data');
        $this->assertCount(2, $data);

        $strayMarker = collect($data)->firstWhere('name', 'Si Belang Pasar');
        $petMarker = collect($data)->firstWhere('name', 'Mimi');

        $this->assertEquals('stray', $strayMarker['ownership_type']);
        $this->assertEquals('#ea580c', $strayMarker['pin_visual']['bg_color']);
        $this->assertEquals('Kucing Liar Sekitar', $strayMarker['pin_visual']['label']);

        $this->assertEquals('pet', $petMarker['ownership_type']);
        $this->assertEquals('#0f766e', $petMarker['pin_visual']['bg_color']);
        $this->assertEquals('Kucing Peliharaan Member', $petMarker['pin_visual']['label']);
    }

    public function test_community_member_can_submit_update_to_peta_anabul()
    {
        $cat = PetaAnabul::create([
            'user_id' => $this->member->id,
            'ownership_type' => 'stray',
            'name' => 'Oyen Malioboro',
            'unique_code' => PetaAnabul::generateUniqueCode(),
            'gender' => 'jantan',
            'condition' => 'terluka',
            'sterilization_status' => 'tidak_diketahui',
            'latitude' => -7.7928,
            'longitude' => 110.3658,
            'status' => 'aktif',
        ]);

        $updateData = [
            'update_type' => 'handled',
            'condition' => 'sehat',
            'status' => 'sudah_ditangani',
            'notes' => 'Kucing sudah dievakuasi ke klinik oleh komunitas dan luka sudah diobati.',
        ];

        $response = $this->actingAs($this->otherMember)->post(route('peta-anabul.update-info', $cat->id), $updateData);
        $response->assertRedirect(route('peta-anabul.show', $cat->id));

        $this->assertDatabaseHas('peta_anabul_updates', [
            'peta_anabul_id' => $cat->id,
            'user_id' => $this->otherMember->id,
            'update_type' => 'handled',
        ]);

        $cat->refresh();
        $this->assertEquals('sudah_ditangani', $cat->status);
        $this->assertEquals('sehat', $cat->condition);
    }

    public function test_temu_anabul_flow_lost_found_and_reunion()
    {
        // 1. Member reports Lost Cat
        $lostData = [
            'type' => 'lost',
            'title' => 'Dicari Kucing Putih Hilang Dekat Tugu',
            'cat_name' => 'Snowy',
            'gender' => 'jantan',
            'coat_color' => 'Putih Bersih',
            'coat_pattern' => 'Solid',
            'distinctive_features' => 'Mata biru, kalung kulit merah ada lonceng',
            'condition' => 'sehat',
            'incident_date' => now()->format('Y-m-d H:i:s'),
            'latitude' => -7.7828,
            'longitude' => 110.3670,
            'city' => 'Kota Yogyakarta',
            'contact_name' => 'Budi Santoso',
            'contact_phone' => '08123456789',
            'is_contact_public' => 1,
            'reward_info' => 'Rp 500.000 bagi yang mengembalikan',
        ];

        $responseLost = $this->actingAs($this->member)->post(route('temu-anabul.store'), $lostData);
        $responseLost->assertRedirect();

        $lostReport = TemuAnabul::where('cat_name', 'Snowy')->first();
        $this->assertNotNull($lostReport);
        $this->assertTrue($lostReport->isLost());
        $this->assertEquals('#e11d48', $lostReport->pin_visual['bg_color']);

        // 2. Another Member reports Found Cat matching Snowy in same city
        $foundData = [
            'type' => 'found',
            'title' => 'Ditemukan Kucing Putih Kalung Merah',
            'cat_name' => 'Kucing Putih Kalung Merah',
            'gender' => 'jantan',
            'coat_color' => 'Putih Bersih',
            'coat_pattern' => 'Solid',
            'distinctive_features' => 'Ada kalung merah dengan lonceng perak',
            'condition' => 'sehat',
            'incident_date' => now()->format('Y-m-d H:i:s'),
            'latitude' => -7.7850,
            'longitude' => 110.3680,
            'city' => 'Kota Yogyakarta',
            'contact_name' => 'Siti Rahma',
            'contact_phone' => '08987654321',
            'is_contact_public' => 1,
            'holding_status' => 'kept_by_finder',
        ];

        $responseFound = $this->actingAs($this->otherMember)->post(route('temu-anabul.store'), $foundData);
        $responseFound->assertRedirect();

        $foundReport = TemuAnabul::where('cat_name', 'Kucing Putih Kalung Merah')->first();
        $this->assertNotNull($foundReport);
        $this->assertTrue($foundReport->isFound());
        $this->assertEquals('#7c3aed', $foundReport->pin_visual['bg_color']);

        // 3. Verify Smart Matching on Lost Report suggests Found Report
        $potentialMatches = $lostReport->getPotentialMatches();
        $this->assertTrue($potentialMatches->contains('id', $foundReport->id));

        // 4. Submit Sighting Tip on Lost Report
        $sightingData = [
            'notes' => 'Melihat kucing putih kalung merah di gang dekat apotek jam 8 malam.',
            'location_name' => 'Jl. AM Sangaji',
            'contact_phone' => '08987654321',
        ];

        $responseSighting = $this->actingAs($this->otherMember)->post(route('temu-anabul.sighting', $lostReport->id), $sightingData);
        $responseSighting->assertRedirect();

        $this->assertDatabaseHas('temu_anabul_sightings', [
            'temu_anabul_id' => $lostReport->id,
            'user_id' => $this->otherMember->id,
            'location_name' => 'Jl. AM Sangaji',
        ]);

        // 5. Reporter marks report as Reunited
        $reunionData = [
            'reunion_notes' => 'Alhamdulillah Snowy sudah kembali berkat informasi dari sesama member KucingMU!',
        ];

        $responseReunited = $this->actingAs($this->member)->post(route('temu-anabul.reunited', $lostReport->id), $reunionData);
        $responseReunited->assertRedirect();

        $lostReport->refresh();
        $this->assertTrue($lostReport->isReunited());
        $this->assertEquals('#059669', $lostReport->pin_visual['bg_color']);

        // 6. Check Poster Page accessible by anyone (including guests)
        $posterResponse = $this->get(route('temu-anabul.poster', $lostReport->id));
        $posterResponse->assertStatus(200);
        $posterResponse->assertSee('Snowy');
    }

    /**
     * Requirement: /temu-anabul/lapor?type=lost ada pilihan bisa ambil kucing dari yg sudah didaftarkan atau belum (untuk membantu orang lain menemukan kucingnya).
     */
    public function test_temu_anabul_lost_cat_options_registered_vs_unregistered()
    {
        // 0. Create a registered cat for member
        $memberCat = Cat::create([
            'user_id' => $this->member->id,
            'name' => 'Mimi',
            'breed' => 'Persia Mix',
            'gender' => 'female',
            'status' => 'alive',
            'color' => 'Abu-abu',
            'photo_path' => 'cats/mimi.jpg',
            'date_of_birth' => '2023-01-01',
        ]);

        // 1. Check create view renders both options
        $viewResponse = $this->actingAs($this->member)->get(route('temu-anabul.create', ['type' => 'lost']));
        $viewResponse->assertStatus(200);
        $viewResponse->assertSee('Kucing Saya yang Sudah Terdaftar', false);
        $viewResponse->assertSee('Kucing Belum Terdaftar / Bantu Orang Lain', false);
        $viewResponse->assertSee('name="lost_source"', false);

        // 2. Case A: Report lost cat choosing from registered cat (Auto-syncs data)
        $lostRegisteredData = [
            'type' => 'lost',
            'cat_id' => $memberCat->id,
            'cat_name' => $memberCat->name,
            'gender' => 'betina',
            'distinctive_features' => 'Kalung ungu berlonceng',
            'condition' => 'terawat',
            'incident_date' => now()->format('Y-m-d H:i:s'),
            'latitude' => -7.7956,
            'longitude' => 110.3695,
            'contact_name' => 'Budi Member',
            'contact_phone' => '08123456789',
        ];

        $postRegistered = $this->actingAs($this->member)->post(route('temu-anabul.store'), $lostRegisteredData);
        $postRegistered->assertRedirect();

        $registeredReport = TemuAnabul::where('cat_id', $memberCat->id)->first();
        $this->assertNotNull($registeredReport);
        $this->assertEquals($memberCat->id, $registeredReport->cat_id);
        $this->assertEquals($memberCat->name, $registeredReport->cat_name);
        $this->assertTrue($registeredReport->isRegisteredCat());
        // Since no photo was uploaded, the registered cat's photo should be copied to report photos
        $this->assertDatabaseHas('temu_anabul_photos', [
            'temu_anabul_id' => $registeredReport->id,
            'photo_path' => 'cats/mimi.jpg',
            'is_primary' => true,
        ]);

        // Detail page shows registered cat badge
        $showRegResponse = $this->get(route('temu-anabul.show', $registeredReport->id));
        $showRegResponse->assertStatus(200);
        $showRegResponse->assertSee('Kucing Terdaftar Resmi KucingMu', false);

        // 3. Case B: Report lost cat for an unregistered cat / helping someone else (cat_id null)
        $lostHelpingOtherData = [
            'type' => 'lost',
            'cat_id' => null,
            'title' => 'Dicari Kucing Tetangga Hilang: Si Belang',
            'cat_name' => 'Si Belang',
            'gender' => 'jantan',
            'coat_color' => 'Hitam Putih',
            'coat_pattern' => 'Tuxedo',
            'distinctive_features' => 'Hidung hitam sebelah, ekor pendek bundel',
            'condition' => 'sehat',
            'incident_date' => now()->format('Y-m-d H:i:s'),
            'latitude' => -7.8010,
            'longitude' => 110.3640,
            'contact_name' => 'Pak RT Joko (Pemilik Asli)',
            'contact_phone' => '087711223344',
            'is_contact_public' => 1,
            'reward_info' => 'Ucapan terima kasih & sekantong makanan kucing',
            'notes' => 'Membantu tetangga RT 05 yang sedang mencari kucing kesayangannya.',
        ];

        $postHelping = $this->actingAs($this->member)->post(route('temu-anabul.store'), $lostHelpingOtherData);
        $postHelping->assertRedirect();

        $helpingReport = TemuAnabul::where('cat_name', 'Si Belang')->first();
        $this->assertNotNull($helpingReport);
        $this->assertNull($helpingReport->cat_id);
        $this->assertEquals('Pak RT Joko (Pemilik Asli)', $helpingReport->contact_name);
        $this->assertEquals('087711223344', $helpingReport->contact_phone);
        $this->assertFalse($helpingReport->isRegisteredCat());

        // Detail page shows community helper badge
        $showHelpingResponse = $this->get(route('temu-anabul.show', $helpingReport->id));
        $showHelpingResponse->assertStatus(200);
        $showHelpingResponse->assertSee('Laporan Kucing Hilang Komunitas (Belum Terdaftar / Membantu Orang Lain)', false);
    }
}
