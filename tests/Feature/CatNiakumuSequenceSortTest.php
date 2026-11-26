<?php

namespace Tests\Feature;

use App\Models\Cat;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CatNiakumuSequenceSortTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_dashboard_sorts_cats_by_niakumu_sequence_number_ignoring_wilayah(): void
    {
        $admin = User::create([
            'name' => 'Admin KucingMu',
            'email' => 'admin_sort@kucingmu.test',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'roles' => ['admin', 'member'],
        ]);

        $owner = User::create([
            'name' => 'Pemilik Kucing',
            'email' => 'owner_sort@kucingmu.test',
            'password' => bcrypt('password'),
            'role' => 'member',
            'roles' => ['member'],
        ]);

        // Cat A has region 35.73, but sequence 1
        $catA = Cat::create([
            'user_id' => $owner->id,
            'name' => 'Kucing Urut Satu',
            'breed' => 'Domestik',
            'gender' => 'male',
            'status' => 'alive',
            'unique_code' => '35.73.kcg.0001',
            'date_of_birth' => now()->subYears(2),
        ]);

        // Cat B has region 31, sequence 2
        $catB = Cat::create([
            'user_id' => $owner->id,
            'name' => 'Kucing Urut Dua',
            'breed' => 'Persia',
            'gender' => 'female',
            'status' => 'alive',
            'unique_code' => '31.kcg.0002',
            'date_of_birth' => now()->subYears(3),
        ]);

        // Cat C has region 34, sequence 3
        $catC = Cat::create([
            'user_id' => $owner->id,
            'name' => 'Kucing Urut Tiga',
            'breed' => 'Siam',
            'gender' => 'male',
            'status' => 'alive',
            'unique_code' => '34.kcg.0003',
            'date_of_birth' => now()->subYears(1),
        ]);

        // Cat D has region 11, sequence 65
        $catD = Cat::create([
            'user_id' => $owner->id,
            'name' => 'Kucing Urut Enam Lima',
            'breed' => 'Anggora',
            'gender' => 'female',
            'status' => 'alive',
            'unique_code' => '11.kcg.0065',
            'date_of_birth' => now()->subMonths(8),
        ]);

        // Cat E has no NIAKuMu
        $catE = Cat::create([
            'user_id' => $owner->id,
            'name' => 'Kucing Belum Ada NIAKuMu',
            'breed' => 'Campuran',
            'gender' => 'male',
            'status' => 'alive',
            'unique_code' => null,
            'date_of_birth' => now()->subMonths(5),
        ]);

        // 1. Test Default / ASC sort on /dashboard
        $response = $this->actingAs($admin)->get(route('dashboard'));
        $response->assertStatus(200);

        /** @var \Illuminate\Pagination\LengthAwarePaginator $viewCats */
        $viewCats = $response->viewData('cats');
        $this->assertNotNull($viewCats);

        $catIds = $viewCats->pluck('id')->toArray();

        // Must be in sequence order: Urut 1 (35.73.kcg.0001), Urut 2 (31.kcg.0002), Urut 3 (34.kcg.0003), Urut 65 (11.kcg.0065), then Null
        $this->assertEquals($catA->id, $catIds[0]);
        $this->assertEquals($catB->id, $catIds[1]);
        $this->assertEquals($catC->id, $catIds[2]);
        $this->assertEquals($catD->id, $catIds[3]);
        $this->assertEquals($catE->id, $catIds[4]);

        // 2. Test Explicit DESC sort
        $descResponse = $this->actingAs($admin)->get(route('dashboard', ['sort' => 'unique_code', 'direction' => 'desc']));
        $descResponse->assertStatus(200);

        /** @var \Illuminate\Pagination\LengthAwarePaginator $descViewCats */
        $descViewCats = $descResponse->viewData('cats');
        $descCatIds = $descViewCats->pluck('id')->toArray();

        // Must be in reverse sequence order: Urut 65, Urut 3, Urut 2, Urut 1, then Null
        $this->assertEquals($catD->id, $descCatIds[0]);
        $this->assertEquals($catC->id, $descCatIds[1]);
        $this->assertEquals($catB->id, $descCatIds[2]);
        $this->assertEquals($catA->id, $descCatIds[3]);
        $this->assertEquals($catE->id, $descCatIds[4]);
    }

    public function test_verifikator_dashboard_sorts_cats_by_niakumu_sequence_number_ignoring_wilayah(): void
    {
        $verifikator = User::create([
            'name' => 'Verifikator KTAKuMu',
            'email' => 'verifikator_sort@kucingmu.test',
            'password' => bcrypt('password'),
            'role' => 'verifikator',
            'roles' => ['verifikator', 'member'],
        ]);

        $owner = User::create([
            'name' => 'Pemilik Kucing',
            'email' => 'owner_veri@kucingmu.test',
            'password' => bcrypt('password'),
            'role' => 'member',
            'roles' => ['member'],
        ]);

        // Cat 1: Region 33, Seq 10
        $cat1 = Cat::create([
            'user_id' => $owner->id,
            'name' => 'Kucing Sepuluh',
            'breed' => 'Domestik',
            'gender' => 'male',
            'status' => 'alive',
            'unique_code' => '33.kcg.0010',
            'date_of_birth' => now()->subYears(2),
        ]);

        // Cat 2: Region 12, Seq 5
        $cat2 = Cat::create([
            'user_id' => $owner->id,
            'name' => 'Kucing Lima',
            'breed' => 'Persia',
            'gender' => 'female',
            'status' => 'alive',
            'unique_code' => '12.kcg.0005',
            'date_of_birth' => now()->subYears(3),
        ]);

        // Verifikator loads dashboard: Seq 5 (12.kcg.0005) must come BEFORE Seq 10 (33.kcg.0010) even though 12 < 33
        $response = $this->actingAs($verifikator)->get(route('dashboard'));
        $response->assertStatus(200);

        /** @var \Illuminate\Pagination\LengthAwarePaginator $viewCats */
        $viewCats = $response->viewData('cats');
        $catIds = $viewCats->pluck('id')->toArray();

        $this->assertEquals($cat2->id, $catIds[0]);
        $this->assertEquals($cat1->id, $catIds[1]);
    }
}
