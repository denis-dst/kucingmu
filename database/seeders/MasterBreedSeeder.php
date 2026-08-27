<?php

namespace Database\Seeders;

use App\Models\MasterBreed;
use Illuminate\Database\Seeder;

class MasterBreedSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $order = 1;
        foreach (MasterBreed::DEFAULT_BREEDS as $breed) {
            MasterBreed::firstOrCreate(
                ['name' => $breed],
                [
                    'is_default' => true,
                    'order' => $order++,
                ]
            );
        }
    }
}
