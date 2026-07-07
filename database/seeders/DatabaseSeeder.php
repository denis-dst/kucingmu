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
            'muhammadiyah_id' => 'NBM-ADMIN-01',
        ]);

        // Dokter / Vet
        User::create([
            'name' => 'Drh. Ahmad',
            'email' => 'dokter@kucingmu.com',
            'password' => bcrypt('password'),
            'role' => 'dokter',
            'phone' => '081234567891',
            'muhammadiyah_id' => 'NBM-DOKTER-01',
        ]);

        // Volunteer
        User::create([
            'name' => 'Relawan Budi',
            'email' => 'relawan@kucingmu.com',
            'password' => bcrypt('password'),
            'role' => 'volunteer',
            'phone' => '081234567892',
            'muhammadiyah_id' => 'NBM-RELAWAN-01',
        ]);

        // Member
        User::create([
            'name' => 'Siti Pemilik Kucing',
            'email' => 'member@kucingmu.com',
            'password' => bcrypt('password'),
            'role' => 'member',
            'phone' => '081234567893',
            'muhammadiyah_id' => 'NBM-MEMBER-01',
        ]);
    }
}
