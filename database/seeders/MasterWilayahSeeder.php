<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MasterWilayah;

class MasterWilayahSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $wilayahs = [
            ['kode' => '34', 'nama' => 'D.I. Yogyakarta (PWM DIY)', 'singkatan' => 'DIY', 'urutan' => 1],
            ['kode' => '33', 'nama' => 'Jawa Tengah (PWM Jateng)', 'singkatan' => 'JATENG', 'urutan' => 2],
            ['kode' => '35', 'nama' => 'Jawa Timur (PWM Jatim)', 'singkatan' => 'JATIM', 'urutan' => 3],
            ['kode' => '31', 'nama' => 'DKI Jakarta (PWM DKI)', 'singkatan' => 'DKI', 'urutan' => 4],
            ['kode' => '32', 'nama' => 'Jawa Barat (PWM Jabar)', 'singkatan' => 'JABAR', 'urutan' => 5],
            ['kode' => '36', 'nama' => 'Banten (PWM Banten)', 'singkatan' => 'BANTEN', 'urutan' => 6],
            ['kode' => '11', 'nama' => 'Aceh (PWM Aceh)', 'singkatan' => 'ACEH', 'urutan' => 7],
            ['kode' => '12', 'nama' => 'Sumatera Utara (PWM Sumut)', 'singkatan' => 'SUMUT', 'urutan' => 8],
            ['kode' => '13', 'nama' => 'Sumatera Barat (PWM Sumbar)', 'singkatan' => 'SUMBAR', 'urutan' => 9],
            ['kode' => '14', 'nama' => 'Riau (PWM Riau)', 'singkatan' => 'RIAU', 'urutan' => 10],
            ['kode' => '15', 'nama' => 'Jambi (PWM Jambi)', 'singkatan' => 'JAMBI', 'urutan' => 11],
            ['kode' => '16', 'nama' => 'Sumatera Selatan (PWM Sumsel)', 'singkatan' => 'SUMSEL', 'urutan' => 12],
            ['kode' => '17', 'nama' => 'Bengkulu (PWM Bengkulu)', 'singkatan' => 'BENGKULU', 'urutan' => 13],
            ['kode' => '18', 'nama' => 'Lampung (PWM Lampung)', 'singkatan' => 'LAMPUNG', 'urutan' => 14],
            ['kode' => '19', 'nama' => 'Kep. Bangka Belitung (PWM Babel)', 'singkatan' => 'BABEL', 'urutan' => 15],
            ['kode' => '21', 'nama' => 'Kepulauan Riau (PWM Kepri)', 'singkatan' => 'KEPRI', 'urutan' => 16],
            ['kode' => '51', 'nama' => 'Bali (PWM Bali)', 'singkatan' => 'BALI', 'urutan' => 17],
            ['kode' => '52', 'nama' => 'Nusa Tenggara Barat (PWM NTB)', 'singkatan' => 'NTB', 'urutan' => 18],
            ['kode' => '53', 'nama' => 'Nusa Tenggara Timur (PWM NTT)', 'singkatan' => 'NTT', 'urutan' => 19],
            ['kode' => '61', 'nama' => 'Kalimantan Barat (PWM Kalbar)', 'singkatan' => 'KALBAR', 'urutan' => 20],
            ['kode' => '62', 'nama' => 'Kalimantan Tengah (PWM Kalteng)', 'singkatan' => 'KALTENG', 'urutan' => 21],
            ['kode' => '63', 'nama' => 'Kalimantan Selatan (PWM Kalsel)', 'singkatan' => 'KALSEL', 'urutan' => 22],
            ['kode' => '64', 'nama' => 'Kalimantan Timur (PWM Kaltim)', 'singkatan' => 'KALTIM', 'urutan' => 23],
            ['kode' => '65', 'nama' => 'Kalimantan Utara (PWM Kaltara)', 'singkatan' => 'KALTARA', 'urutan' => 24],
            ['kode' => '71', 'nama' => 'Sulawesi Utara (PWM Sulut)', 'singkatan' => 'SULUT', 'urutan' => 25],
            ['kode' => '72', 'nama' => 'Sulawesi Tengah (PWM Sulteng)', 'singkatan' => 'SULTENG', 'urutan' => 26],
            ['kode' => '73', 'nama' => 'Sulawesi Selatan (PWM Sulsel)', 'singkatan' => 'SULSEL', 'urutan' => 27],
            ['kode' => '74', 'nama' => 'Sulawesi Tenggara (PWM Sultra)', 'singkatan' => 'SULTRA', 'urutan' => 28],
            ['kode' => '75', 'nama' => 'Gorontalo (PWM Gorontalo)', 'singkatan' => 'GORONTALO', 'urutan' => 29],
            ['kode' => '76', 'nama' => 'Sulawesi Barat (PWM Sulbar)', 'singkatan' => 'SULBAR', 'urutan' => 30],
            ['kode' => '81', 'nama' => 'Maluku (PWM Maluku)', 'singkatan' => 'MALUKU', 'urutan' => 31],
            ['kode' => '82', 'nama' => 'Maluku Utara (PWM Malut)', 'singkatan' => 'MALUT', 'urutan' => 32],
            ['kode' => '91', 'nama' => 'Papua (PWM Papua)', 'singkatan' => 'PAPUA', 'urutan' => 33],
            ['kode' => '92', 'nama' => 'Papua Barat (PWM Papbar)', 'singkatan' => 'PAPBAR', 'urutan' => 34],
            ['kode' => '00', 'nama' => 'Pusat (PP Muhammadiyah)', 'singkatan' => 'PUSAT', 'urutan' => 0],
        ];

        foreach ($wilayahs as $w) {
            MasterWilayah::updateOrCreate(
                ['kode' => $w['kode']],
                [
                    'nama' => $w['nama'],
                    'singkatan' => $w['singkatan'],
                    'urutan' => $w['urutan'],
                    'is_active' => true,
                ]
            );
        }
    }
}
