<?php

namespace Tests\Feature;

use App\Models\Cat;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CatMemorialRouteBindingTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_access_memorial_page_by_id_unique_code_and_slug(): void
    {
        $owner = User::factory()->create([
            'role' => 'member',
            'name' => 'Azman Latif',
        ]);

        $cat = Cat::create([
            'user_id' => $owner->id,
            'name' => 'Messi',
            'breed' => 'Mix Ras',
            'gender' => 'female',
            'color' => 'Calico',
            'status' => 'mati',
            'date_of_birth' => '2011-01-01',
            'unique_code' => '12.kcg.0001',
        ]);

        $visitor = User::factory()->create([
            'role' => 'member',
            'name' => 'Kucing Lover',
        ]);

        // 1. By ID
        $responseId = $this->actingAs($visitor)->get(route('cat.memorial', $cat->id));
        $responseId->assertStatus(200);
        $responseId->assertSee('Messi');
        $responseId->assertSee('text-slate-900');
        $responseId->assertSee('12.kcg.0001');

        // 2. By unique_code (e.g. /cat/12.kcg.0001/memorial)
        $responseCode = $this->actingAs($visitor)->get('/cat/' . $cat->unique_code . '/memorial');
        $responseCode->assertStatus(200);
        $responseCode->assertSee('Messi');
        $responseCode->assertSee('Azman Latif');

        // 3. By slug (e.g. /cat/messi/memorial)
        $responseSlug = $this->actingAs($visitor)->get('/cat/messi/memorial');
        $responseSlug->assertStatus(200);
        $responseSlug->assertSee('Messi');
        $responseSlug->assertSee('Mix Ras');
    }

    public function test_can_access_memorial_page_by_hyphenated_slug(): void
    {
        $owner = User::factory()->create([
            'role' => 'member',
            'name' => 'Budi Santoso',
        ]);

        $cat = Cat::create([
            'user_id' => $owner->id,
            'name' => 'Oyen Gemoy',
            'breed' => 'Domestik',
            'gender' => 'male',
            'color' => 'Oranye',
            'status' => 'mati',
            'date_of_birth' => '2021-05-10',
            'unique_code' => '12.kcg.0002',
        ]);

        $visitor = User::factory()->create([
            'role' => 'member',
        ]);

        // Access by hyphenated slug /cat/oyen-gemoy/memorial
        $response = $this->actingAs($visitor)->get('/cat/oyen-gemoy/memorial');
        $response->assertStatus(200);
        $response->assertSee('Oyen Gemoy');
        $response->assertSee('Domestik');
        $response->assertSee('12.kcg.0002');
    }
}
