<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class KtamCard extends Model
{
    use HasFactory;

    protected $fillable = [
        'cat_id',
        'ktam_number',
        'issue_date',
        'qr_code_payload',
        'is_printed',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'is_printed' => 'boolean',
    ];

    public function cat()
    {
        return $this->belongsTo(Cat::class);
    }

    /**
     * Generate QR code payload dynamically using the current APP_URL configuration.
     */
    public function getQrCodePayloadAttribute()
    {
        $verificationUrl = route('ktam.verify', ['number' => $this->ktam_number]);
        
        $qrCodeSvg = \SimpleSoftwareIO\QrCode\Facades\QrCode::size(200)
            ->color(6, 29, 18)
            ->backgroundColor(255, 255, 255)
            ->generate($verificationUrl);

        return 'data:image/svg+xml;base64,' . base64_encode($qrCodeSvg);
    }
}
