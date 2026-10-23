<?php

namespace Tests\Feature;

use App\Models\Cat;
use App\Models\Event;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CatRegistrationSuccessModalTest extends TestCase
{
    use RefreshDatabase;

    private User $member;

    protected function setUp(): void
    {
        parent::setUp();

        $this->member = User::factory()->create([
            'role' => 'member',
            'roles' => ['member'],
        ]);
    }

    public function test_member_registration_redirects_with_cat_registered_flash_session(): void
    {
        Storage::fake('public');

        $response = $this->actingAs($this->member)->post(route('cat.store'), [
            'name' => 'Mimi Cantik',
            'breed' => 'Domestik',
            'gender' => 'female',
            'status' => 'alive',
            'date_of_birth' => '2024-01-15',
            'photo' => UploadedFile::fake()->image('mimi.jpg'),
        ]);

        $response->assertRedirect(route('dashboard'));
        $response->assertSessionHas('cat_registered', true);
        $response->assertSessionHas('registered_cat_name', 'Mimi Cantik');
    }

    public function test_dashboard_renders_modal_with_verifikasi_event_message_and_event_link(): void
    {
        Event::create([
            'title' => 'Vaksinasi Massal KucingMu Jogja',
            'description' => 'Verifikasi KTAKuMu dan vaksinasi gratis.',
            'date' => now()->addDays(5),
            'location' => 'Balai Pertemuan Sleman',
            'status' => 'active',
        ]);

        $response = $this->actingAs($this->member)
            ->withSession([
                'cat_registered' => true,
                'registered_cat_name' => 'Mimi Cantik',
            ])
            ->get(route('dashboard'));

        $response->assertOk();
        $response->assertSee('Selamat Kucing Anda telah terdaftar pada KucingMu, untuk mendapatkan KTAKuMu perlu dilakukan Verifikasi secara langsung pada Event KucingMu.', false);
        $response->assertSee('Lihat Event KucingMu disini', false);
        $response->assertSee('href="#events-section"', false);
        $response->assertSee('Lihat Event KucingMu Disini', false);
        $response->assertSee('id="events-section"', false);
    }

    public function test_events_section_is_present_even_when_no_active_events_exist(): void
    {
        $response = $this->actingAs($this->member)
            ->withSession([
                'cat_registered' => true,
                'registered_cat_name' => 'Kuro',
            ])
            ->get(route('dashboard'));

        $response->assertOk();
        $response->assertSee('id="events-section"', false);
        $response->assertSee('Agenda & Event KucingMu', false);
        $response->assertSee('Lihat Event KucingMu Disini', false);
    }
}
