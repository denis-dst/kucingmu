<?php

namespace App\Services;

use App\Models\Cat;
use App\Models\KtamCard;
use Carbon\Carbon;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class KtamService
{
    /**
     * Issue a new KTAM Card for a cat if it does not already have one.
     *
     * @param  \App\Models\Cat  $cat
     * @param  int|null $adminId
     * @return \App\Models\KtamCard
     */
    public function issueCard(Cat $cat, ?int $adminId = null): KtamCard
    {
        // Return existing card if already issued
        if ($cat->ktamCard) {
            return $cat->ktamCard;
        }

        // Ensure wilayah_code and unique_code exist
        if (empty($cat->wilayah_code)) {
            $cat->wilayah_code = '34';
        }
        if (empty($cat->unique_code)) {
            $cat->unique_code = Cat::generateUniqueCode($cat->wilayah_code, $cat->id);
            $cat->saveQuietly();
        }

        $ktamNumber = $cat->unique_code;
        $verificationUrl = route('ktam.verify', ['number' => $ktamNumber]);

        // Generate QR code SVG content as base64 string
        $qrCodeSvg = QrCode::size(200)
            ->color(15, 118, 110) // Teal color matching theme
            ->backgroundColor(255, 255, 255)
            ->generate($verificationUrl);

        $qrCodeBase64 = 'data:image/svg+xml;base64,' . base64_encode($qrCodeSvg);

        return KtamCard::create([
            'cat_id' => $cat->id,
            'ktam_number' => $ktamNumber,
            'issue_date' => Carbon::today(),
            'qr_code_payload' => $qrCodeBase64,
            'is_printed' => false,
            'verified_by' => $adminId,
            'verified_at' => Carbon::now(),
        ]);
    }

    /**
     * Generate a unique KTAM number for a cat based on wilayah and ID.
     *
     * @param \App\Models\Cat|null $cat
     * @return string
     */
    public function generateKtamNumber(?Cat $cat = null): string
    {
        if ($cat) {
            return $cat->unique_code ?: Cat::generateUniqueCode($cat->wilayah_code ?: '34', $cat->id);
        }
        return '34.kcg.' . str_pad(Cat::count() + 1, 4, '0', STR_PAD_LEFT);
    }
}
