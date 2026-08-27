<?php

namespace Database\Seeders;

use App\Models\Cat;
use App\Models\KtamCard;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class LegacyCatMembersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $defaultPassword = Hash::make('password!1');

        $dataset = [
            [
                'owner' => [
                    'name'            => 'Nur Fianto',
                    'phone'           => '081328604924',
                    'muhammadiyah_id' => null,
                    'address'         => null,
                ],
                'cat' => [
                    'id'            => 6,
                    'name'          => 'BigShe',
                    'breed'         => 'Lokal',
                    'gender'        => 'female',
                    'color'         => 'Putih ekor Abu-abu',
                    'date_of_birth' => '2026-03-01',
                    'unique_code'   => '12.kcg.0006',
                    'wilayah_code'  => '12',
                    'notes'         => 'Data registrasi legacy pra-sistem.',
                ],
            ],
            [
                'owner' => [
                    'name'            => 'Laila Maulidina',
                    'phone'           => '089674241060',
                    'muhammadiyah_id' => null,
                    'address'         => null,
                ],
                'cat' => [
                    'id'            => 11,
                    'name'          => 'Boyy',
                    'breed'         => 'Domestik',
                    'gender'        => 'male',
                    'color'         => 'Oren Putih',
                    'date_of_birth' => '2025-06-01',
                    'unique_code'   => '12.kcg.0011',
                    'wilayah_code'  => '12',
                    'notes'         => 'Data registrasi legacy pra-sistem.',
                ],
            ],
            [
                'owner' => [
                    'name'            => 'Vira Melinda',
                    'phone'           => '082137286876',
                    'muhammadiyah_id' => '12.KCG.0038',
                    'address'         => null,
                ],
                'cat' => [
                    'id'            => 38,
                    'name'          => 'Piu',
                    'breed'         => 'Ras himalaya',
                    'gender'        => 'female',
                    'color'         => 'Putih',
                    'date_of_birth' => '2025-12-01',
                    'unique_code'   => '12.kcg.0038',
                    'wilayah_code'  => '12',
                    'notes'         => 'Data registrasi legacy pra-sistem (usia saat daftar ~4 bln).',
                ],
            ],
            [
                'owner' => [
                    'name'            => 'Hasheena Jasmine Pamena',
                    'phone'           => '085932861148',
                    'muhammadiyah_id' => '1410164',
                    'address'         => null,
                ],
                'cat' => [
                    'id'            => 5,
                    'name'          => 'Kayya',
                    'breed'         => 'Domestik (Mujair)',
                    'gender'        => 'female',
                    'color'         => 'Tabby',
                    'date_of_birth' => '2026-04-01',
                    'unique_code'   => '12.kcg.0005',
                    'wilayah_code'  => '12',
                    'notes'         => 'Data registrasi legacy pra-sistem.',
                ],
            ],
            [
                'owner' => [
                    'name'            => 'Nur Akhda Sabila',
                    'phone'           => '082314842823',
                    'muhammadiyah_id' => '1398279 / 120295211398279',
                    'address'         => null,
                ],
                'cat' => [
                    'id'            => 25,
                    'name'          => 'Ucil',
                    'breed'         => 'Domestik',
                    'gender'        => 'male',
                    'color'         => 'Putih Abu',
                    'date_of_birth' => '2026-01-01',
                    'unique_code'   => '12.kcg.0025',
                    'wilayah_code'  => '12',
                    'notes'         => 'Data registrasi legacy pra-sistem.',
                ],
            ],
            [
                'owner' => [
                    'name'            => 'Hertini',
                    'phone'           => '085328982180',
                    'muhammadiyah_id' => '12.kcmg.02',
                    'address'         => null,
                ],
                'cat' => [
                    'id'            => 2,
                    'name'          => 'Gree',
                    'breed'         => 'Campuran Jawa BSH',
                    'gender'        => 'male',
                    'color'         => 'Abu campur putih di dada',
                    'date_of_birth' => '2025-02-14',
                    'unique_code'   => '12.kcg.0002',
                    'wilayah_code'  => '12',
                    'notes'         => 'Data registrasi legacy pra-sistem.',
                ],
            ],
            [
                'owner' => [
                    'name'            => 'Muhammad Faiz Muzaffar',
                    'phone'           => '089518561951',
                    'muhammadiyah_id' => '120303221437480',
                    'address'         => null,
                ],
                'cat' => [
                    'id'            => 14,
                    'name'          => 'Brothuk',
                    'breed'         => 'Domestik',
                    'gender'        => 'male',
                    'color'         => 'Oren',
                    'date_of_birth' => '2025-07-01',
                    'unique_code'   => '12.kcg.0014',
                    'wilayah_code'  => '12',
                    'notes'         => 'Data registrasi legacy pra-sistem.',
                ],
            ],
            [
                'owner' => [
                    'name'            => 'Dyah Ngesti Rahayu Lestari',
                    'phone'           => '081804304699',
                    'muhammadiyah_id' => null,
                    'address'         => 'Nglempongsari',
                ],
                'cat' => [
                    'id'            => 8,
                    'name'          => 'Inul Ratnasari (kalung cokelat)',
                    'breed'         => 'Domestik',
                    'gender'        => 'female',
                    'color'         => 'Oren Putih',
                    'date_of_birth' => '2025-10-23',
                    'unique_code'   => '12.kcg.0008',
                    'wilayah_code'  => '12',
                    'notes'         => 'Data registrasi legacy pra-sistem.',
                ],
            ],
            [
                'owner' => [
                    'name'            => 'Ayu Rahmadhani',
                    'phone'           => '089687519590',
                    'muhammadiyah_id' => null,
                    'address'         => 'Pelem mulong kampung',
                ],
                'cat' => [
                    'id'            => 48,
                    'name'          => 'Leon',
                    'breed'         => 'Domestik',
                    'gender'        => 'male',
                    'color'         => 'Abu',
                    'date_of_birth' => '2026-03-01',
                    'unique_code'   => '12.kcg.0048',
                    'wilayah_code'  => '12',
                    'notes'         => 'Data registrasi legacy pra-sistem.',
                ],
            ],
            [
                'owner' => [
                    'name'            => 'Dyah Ngesti Rahayu Lestari',
                    'phone'           => '081804304699',
                    'muhammadiyah_id' => null,
                    'address'         => 'Nglempongsari',
                ],
                'cat' => [
                    'id'            => 7,
                    'name'          => 'Benierka',
                    'breed'         => 'Domestik',
                    'gender'        => 'female',
                    'color'         => 'Oren Putih',
                    'date_of_birth' => '2025-06-03',
                    'unique_code'   => '12.kcg.0007',
                    'wilayah_code'  => '12',
                    'notes'         => 'Data registrasi legacy pra-sistem.',
                ],
            ],
            [
                'owner' => [
                    'name'            => 'Daffa Fauzia Rohman',
                    'phone'           => '087829403895',
                    'muhammadiyah_id' => '1564922',
                    'address'         => 'RT. 11 RW. 04, Caturtunggal',
                ],
                'cat' => [
                    'id'            => 9,
                    'name'          => 'Abu',
                    'breed'         => 'Domestik',
                    'gender'        => 'male',
                    'color'         => 'Abu-abu',
                    'date_of_birth' => '2025-01-01',
                    'unique_code'   => '12.kcg.0009',
                    'wilayah_code'  => '12',
                    'notes'         => 'Data registrasi legacy pra-sistem.',
                ],
            ],
            [
                'owner' => [
                    'name'            => 'Endang',
                    'phone'           => '08158985185',
                    'muhammadiyah_id' => null,
                    'address'         => 'Perumahan Muslim',
                ],
                'cat' => [
                    'id'            => 3,
                    'name'          => 'Tom',
                    'breed'         => 'Domestik',
                    'gender'        => 'male',
                    'color'         => 'Putih orange',
                    'date_of_birth' => '2022-12-01',
                    'unique_code'   => '12.kcg.0003',
                    'wilayah_code'  => '12',
                    'notes'         => 'Data registrasi legacy pra-sistem.',
                ],
            ],
            [
                'owner' => [
                    'name'            => 'Frida Zuhera Yustisiani Farid',
                    'phone'           => '081578711938',
                    'muhammadiyah_id' => '1100154',
                    'address'         => 'Diro RT 58 Pendowoharjo',
                ],
                'cat' => [
                    'id'            => 47,
                    'name'          => 'Neko',
                    'breed'         => 'Mixdom',
                    'gender'        => 'female',
                    'color'         => 'Putih corak oranye',
                    'date_of_birth' => '2024-07-01',
                    'unique_code'   => '12.kcg.0047',
                    'wilayah_code'  => '12',
                    'notes'         => 'Data registrasi legacy pra-sistem.',
                ],
            ],
            [
                'owner' => [
                    'name'            => 'Azzahra Sajida Salmadevi',
                    'phone'           => '085865240893',
                    'muhammadiyah_id' => null,
                    'address'         => 'Modalan Baru RT 1, Banguntapan',
                ],
                'cat' => [
                    'id'            => 40,
                    'name'          => 'Budi',
                    'breed'         => 'Mixdom',
                    'gender'        => 'male',
                    'color'         => 'Hitam-putih',
                    'date_of_birth' => '2026-02-23',
                    'unique_code'   => '12.kcg.0040',
                    'wilayah_code'  => '12',
                    'notes'         => 'Data registrasi legacy pra-sistem.',
                ],
            ],
            [
                'owner' => [
                    'name'            => 'Azzahra Sajida Salmadevi',
                    'phone'           => '085865240893',
                    'muhammadiyah_id' => null,
                    'address'         => 'Modalan Baru RT01, Banguntapan',
                ],
                'cat' => [
                    'id'            => 42,
                    'name'          => 'Devil',
                    'breed'         => 'Mixdom',
                    'gender'        => 'male',
                    'color'         => 'Abu2',
                    'date_of_birth' => '2026-02-23',
                    'unique_code'   => '12.kcg.0042',
                    'wilayah_code'  => '12',
                    'notes'         => 'Data registrasi legacy pra-sistem.',
                ],
            ],
            [
                'owner' => [
                    'name'            => 'Alfa Fitri Amalia Hilal',
                    'phone'           => '081932701243',
                    'muhammadiyah_id' => null,
                    'address'         => 'Modalan Baru RT 01, Banguntapan',
                ],
                'cat' => [
                    'id'            => 59,
                    'name'          => 'Skill',
                    'breed'         => 'Mixdom',
                    'gender'        => 'female',
                    'color'         => 'Putih',
                    'date_of_birth' => '2026-02-23',
                    'unique_code'   => '12.kcg.0059',
                    'wilayah_code'  => '12',
                    'notes'         => 'Data registrasi legacy pra-sistem.',
                ],
            ],
            [
                'owner' => [
                    'name'            => 'Alfa Fitri Amalia Hilal',
                    'phone'           => '081932701243',
                    'muhammadiyah_id' => null,
                    'address'         => 'Modalan Baru RT 01, Banguntapan',
                ],
                'cat' => [
                    'id'            => 60,
                    'name'          => 'Debu',
                    'breed'         => 'Munchkin x Persia',
                    'gender'        => 'male',
                    'color'         => 'Abu2 putih',
                    'date_of_birth' => '2025-05-01',
                    'unique_code'   => '12.kcg.0060',
                    'wilayah_code'  => '12',
                    'notes'         => 'Data registrasi legacy pra-sistem.',
                ],
            ],
            [
                'owner' => [
                    'name'            => 'Azzahra Sajida Salmadevi',
                    'phone'           => '085865240893',
                    'muhammadiyah_id' => null,
                    'address'         => 'Modalan Baru RT 01, Banguntapan',
                ],
                'cat' => [
                    'id'            => 41,
                    'name'          => 'Kumit',
                    'breed'         => 'Mixdom',
                    'gender'        => 'female',
                    'color'         => 'Putih',
                    'date_of_birth' => '2026-02-23',
                    'unique_code'   => '12.kcg.0041',
                    'wilayah_code'  => '12',
                    'notes'         => 'Data registrasi legacy pra-sistem.',
                ],
            ],
            [
                'owner' => [
                    'name'            => 'Ika Kusumayani',
                    'phone'           => '089637195373',
                    'muhammadiyah_id' => '887520',
                    'address'         => 'Gedongan KG 3/26 Yogyakarta',
                ],
                'cat' => [
                    'id'            => 44,
                    'name'          => 'Liona',
                    'breed'         => 'Himalaya',
                    'gender'        => 'female',
                    'color'         => 'Putih',
                    'date_of_birth' => '2022-03-01',
                    'unique_code'   => '12.kcg.0044',
                    'wilayah_code'  => '12',
                    'notes'         => 'Data registrasi legacy pra-sistem.',
                ],
            ],
            [
                'owner' => [
                    'name'            => 'Nefiora Afia Fitzora',
                    'phone'           => '089605976761',
                    'muhammadiyah_id' => null,
                    'address'         => 'Gedongan KG 3/26 Yogyakarta',
                ],
                'cat' => [
                    'id'            => 45,
                    'name'          => 'White',
                    'breed'         => 'Himalaya',
                    'gender'        => 'male',
                    'color'         => 'Putih',
                    'date_of_birth' => '2023-12-08',
                    'unique_code'   => '12.kcg.0045',
                    'wilayah_code'  => '12',
                    'notes'         => 'Data registrasi legacy pra-sistem.',
                ],
            ],
            [
                'owner' => [
                    'name'            => 'Shefinsa Afia Fitzora',
                    'phone'           => '089616074422',
                    'muhammadiyah_id' => null,
                    'address'         => 'Gedongan KG 3/26 Yogyakarta',
                ],
                'cat' => [
                    'id'            => 46,
                    'name'          => 'Elio',
                    'breed'         => 'Himalaya',
                    'gender'        => 'male',
                    'color'         => 'Putih',
                    'date_of_birth' => '2022-03-01',
                    'unique_code'   => '12.kcg.0046',
                    'wilayah_code'  => '12',
                    'notes'         => 'Data registrasi legacy pra-sistem.',
                ],
            ],
            [
                'owner' => [
                    'name'            => 'Kaharani Annindya Nissa',
                    'phone'           => '085155328871',
                    'muhammadiyah_id' => null,
                    'address'         => 'Tegalgentu KG II/1094 Kotagede',
                ],
                'cat' => [
                    'id'            => 15,
                    'name'          => 'Frangky',
                    'breed'         => 'Mixdom',
                    'gender'        => 'male',
                    'color'         => 'Hitam, Abu, Putih',
                    'date_of_birth' => '2023-01-06',
                    'unique_code'   => '12.kcg.0015',
                    'wilayah_code'  => '12',
                    'notes'         => 'Data registrasi legacy pra-sistem.',
                ],
            ],
            [
                'owner' => [
                    'name'            => 'Azizatur Rohmah',
                    'phone'           => '085843286636',
                    'muhammadiyah_id' => '12.KCG.0023',
                    'address'         => 'Jl. Nglanjaran, Candirejo',
                ],
                'cat' => [
                    'id'            => 23,
                    'name'          => 'Ken',
                    'breed'         => 'Persia Flatnose long hair',
                    'gender'        => 'male',
                    'color'         => 'Cream & Putih',
                    'date_of_birth' => '2025-07-01',
                    'unique_code'   => '12.kcg.0023',
                    'wilayah_code'  => '12',
                    'notes'         => 'Data registrasi legacy pra-sistem.',
                ],
            ],
        ];

        DB::statement('SET FOREIGN_KEY_CHECKS = 0');

        foreach ($dataset as $row) {
            $rawPhone = preg_replace('/[^0-9]/', '', $row['owner']['phone']);
            $email = $rawPhone . '@kucingmu.online';

            // 1. Create or get user
            $user = User::firstOrCreate(
                ['email' => $email],
                [
                    'name'              => $row['owner']['name'],
                    'password'          => $defaultPassword,
                    'phone'             => $rawPhone,
                    'role'              => 'member',
                    'muhammadiyah_id'   => $row['owner']['muhammadiyah_id'],
                    'email_verified_at' => Carbon::now(),
                ]
            );

            // Update info if existing
            $user->update([
                'name'            => $row['owner']['name'],
                'phone'           => $rawPhone,
                'muhammadiyah_id' => $row['owner']['muhammadiyah_id'] ?: $user->muhammadiyah_id,
            ]);

            // 2. Create or update cat
            $catId = $row['cat']['id'];
            $uniqueCode = strtolower(trim($row['cat']['unique_code']));

            $cat = Cat::find($catId);
            if (!$cat) {
                $cat = new Cat();
                $cat->id = $catId;
            }

            $cat->user_id       = $user->id;
            $cat->name          = $row['cat']['name'];
            $cat->breed         = $row['cat']['breed'];
            $cat->gender        = $row['cat']['gender'];
            $cat->date_of_birth = $row['cat']['date_of_birth'];
            $cat->wilayah_code  = $row['cat']['wilayah_code'];
            $cat->unique_code   = $uniqueCode;
            $cat->color         = $row['cat']['color'];
            $cat->notes         = $row['cat']['notes'];
            $cat->saveQuietly();

            // 3. Create or update KtamCard
            $ktamCard = KtamCard::where('cat_id', $cat->id)->first();
            if (!$ktamCard) {
                $ktamCard = new KtamCard();
                $ktamCard->cat_id = $cat->id;
            }

            $verificationUrl = route('ktam.verify', ['number' => $uniqueCode]);
            $qrCodeSvg = QrCode::size(200)
                ->color(15, 118, 110)
                ->backgroundColor(255, 255, 255)
                ->generate($verificationUrl);

            $ktamCard->ktam_number     = $uniqueCode;
            $ktamCard->issue_date      = Carbon::now()->toDateString();
            $ktamCard->qr_code_payload = 'data:image/svg+xml;base64,' . base64_encode($qrCodeSvg);
            $ktamCard->is_printed      = true;
            $ktamCard->save();
        }

        // Set AUTO_INCREMENT pada tabel cats agar data pendaftaran baru berikutnya mulai dari 61 (atau setelah ID tertinggi)
        $maxCatId = (int) DB::table('cats')->max('id');
        $nextAutoIncrement = max($maxCatId + 1, 61);
        DB::statement("ALTER TABLE cats AUTO_INCREMENT = {$nextAutoIncrement}");

        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
}
