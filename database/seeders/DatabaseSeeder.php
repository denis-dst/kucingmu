<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Admin
        User::create([
            'name' => 'Admin KucingMu',
            'email' => 'admin@kucingmu.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'phone' => '081234567890',
            'muhammadiyah_id' => '1.000.001',
        ]);

        // Dokter / Vet
        User::create([
            'name' => 'Drh. Ahmad',
            'email' => 'dokter@kucingmu.com',
            'password' => bcrypt('password'),
            'role' => 'dokter',
            'phone' => '081234567891',
            'muhammadiyah_id' => '1.000.002',
        ]);

        // Volunteer
        User::create([
            'name' => 'Relawan Budi',
            'email' => 'relawan@kucingmu.com',
            'password' => bcrypt('password'),
            'role' => 'volunteer',
            'phone' => '081234567892',
            'muhammadiyah_id' => '1.000.003',
        ]);

        // Member
        User::firstOrCreate(
            ['email' => 'member@kucingmu.com'],
            [
                'name' => 'Siti Pemilik Kucing',
                'password' => bcrypt('password'),
                'role' => 'member',
                'phone' => '081234567893',
                'muhammadiyah_id' => '1.234.567',
            ]
        );

        $this->call([
            MasterWilayahSeeder::class,
            ActivityAlbumSeeder::class,
            LegacyCatMembersSeeder::class,
        ]);
    }
}
