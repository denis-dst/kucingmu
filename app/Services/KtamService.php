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
     * @return \App\Models\KtamCard
     */
    public function issueCard(Cat $cat): KtamCard
    {
        // Return existing card if already issued
        if ($cat->ktamCard) {
            return $cat->ktamCard;
        }

        $ktamNumber = $this->generateKtamNumber();
        $verificationUrl = route('ktam.verify', ['number' => $ktamNumber]);

        // Generate QR code SVG content as base64 string
        $qrCodeSvg = QrCode::size(200)
            ->color(15, 118, 110) // Teal color matching siakad theme
            ->backgroundColor(255, 255, 255)
            ->generate($verificationUrl);

        $qrCodeBase64 = 'data:image/svg+xml;base64,' . base64_encode($qrCodeSvg);

        return KtamCard::create([
            'cat_id' => $cat->id,
            'ktam_number' => $ktamNumber,
            'issue_date' => Carbon::today(),
            'qr_code_payload' => $qrCodeBase64,
            'is_printed' => false,
        ]);
    }

    /**
     * Generate a unique KTAM number: KM-YYYYMMDD-XXXX
     *
     * @return string
     */
    protected function generateKtamNumber(): string
    {
        $todayStr = Carbon::today()->format('Ymd');
        $prefix = 'KM-' . $todayStr . '-';

        // Find the latest card issued today to calculate next sequence
        $latestCard = KtamCard::where('ktam_number', 'like', $prefix . '%')
            ->orderBy('ktam_number', 'desc')
            ->first();

        if ($latestCard) {
            $parts = explode('-', $latestCard->ktam_number);
            $sequence = intval(end($parts)) + 1;
        } else {
            $sequence = 1;
        }

        return $prefix . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }
}
