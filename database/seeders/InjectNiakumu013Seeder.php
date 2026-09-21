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

class InjectNiakumu013Seeder extends Seeder
{
    public function run(): void
    {
        $defaultPassword = Hash::make('password!1');
        $rawPhone = '081944530946';
        $email = $rawPhone . '@kucingmu.online';

        DB::statement('SET FOREIGN_KEY_CHECKS = 0');

        // 1. Create or update user
        $user = User::updateOrCreate(
            ['email' => $email],
            [
                'name' => 'Alfin Bimantara',
                'password' => $defaultPassword,
                'phone' => $rawPhone,
                'role' => 'member',
                'roles' => ['member'],
                'muhammadiyah_id' => '1.20105E+14',
                'bio' => 'Jl. A.M. Sangaji No.66',
                'email_verified_at' => Carbon::now(),
            ]
        );

        // 2. Create or update cat
        $cat = Cat::withTrashed()->find(13);
        if (!$cat) {
            $cat = new Cat();
            $cat->id = 13;
        } else {
            $cat->restore();
        }

        $cat->user_id = $user->id;
        $cat->name = 'Aceng';
        $cat->breed = 'Campuran';
        $cat->gender = 'male';
        $cat->status = 'alive';
        $cat->date_of_birth = '2024-01-01';
        $cat->wilayah_code = '12';
        $cat->unique_code = '12.kcg.0013';
        $cat->color = 'Tabby';
        $cat->notes = 'Data registrasi legacy pra-sistem.';
        $cat->saveQuietly();

        // 3. Create or update KtamCard
        $verificationUrl = route('ktam.verify', ['number' => '12.kcg.0013']);
        $qrCodeSvg = QrCode::size(200)
            ->color(15, 118, 110)
            ->backgroundColor(255, 255, 255)
            ->generate($verificationUrl);

        $ktamCard = KtamCard::updateOrCreate(
            ['cat_id' => 13],
            [
                'ktam_number' => '12.kcg.0013',
                'issue_date' => Carbon::now()->toDateString(),
                'qr_code_payload' => 'data:image/svg+xml;base64,' . base64_encode($qrCodeSvg),
                'is_printed' => true,
                'verified_by' => 1,
                'verified_at' => Carbon::now(),
            ]
        );

        // Update auto increment if needed
        $maxCatId = (int) DB::table('cats')->max('id');
        $nextAutoIncrement = max($maxCatId + 1, 61);
        DB::statement("ALTER TABLE cats AUTO_INCREMENT = {$nextAutoIncrement}");

        DB::statement('SET FOREIGN_KEY_CHECKS = 1');

        $this->command->info("Inject berhasil: User #{$user->id} ({$user->name}), Cat #{$cat->id} ({$cat->name} - {$cat->unique_code}), KTAM Card: {$ktamCard->ktam_number}");
    }
}
