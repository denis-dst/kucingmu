<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class RepresentativeRegistration extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'registration_number',
        'name',
        'nbm',
        'birth_date',
        'email',
        'whatsapp_number',
        'instagram_username',
        'province_name',
        'city_name',
        'district_name',
        'village_name',
        'latitude',
        'longitude',
        'formatted_address',
        'muhammadiyah_active_leadership',
        'sk_pimpinan_document_path',
        'ktam_document_path',
        'animal_welfare_essay',
        'privacy_agreed',
        'status',
        'admin_notes',
        'reviewed_by',
        'reviewed_at',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'privacy_agreed' => 'boolean',
        'latitude' => 'float',
        'longitude' => 'float',
        'reviewed_at' => 'datetime',
    ];

    /**
     * Boot function for automatic registration number generation.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->registration_number)) {
                $model->registration_number = self::generateUniqueNumber();
            }
        });
    }

    /**
     * Generate unique registration number: REP-YYYYMM-XXXX
     */
    public static function generateUniqueNumber(): string
    {
        $prefix = 'REP-' . date('Ym') . '-';
        do {
            $randomCode = strtoupper(Str::random(4));
            $candidate = $prefix . $randomCode;
        } while (self::where('registration_number', $candidate)->exists());

        return $candidate;
    }

    /**
     * User who registered (if logged in).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Reviewer user.
     */
    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    /**
     * Status label in Indonesian.
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'Menunggu Review',
            'reviewed' => 'Sedang Direview',
            'approved' => 'Diterima / Terverifikasi',
            'rejected' => 'Ditolak / Belum Sesuai',
            default => ucfirst($this->status),
        };
    }

    /**
     * Status badge metadata.
     */
    public function getStatusBadgeAttribute(): array
    {
        return match ($this->status) {
            'pending' => [
                'bg' => 'bg-amber-50 text-amber-800 border-amber-200',
                'dot' => 'bg-amber-500',
                'label' => 'Menunggu Review',
            ],
            'reviewed' => [
                'bg' => 'bg-blue-50 text-blue-800 border-blue-200',
                'dot' => 'bg-blue-500',
                'label' => 'Sedang Direview',
            ],
            'approved' => [
                'bg' => 'bg-teal-50 text-teal-800 border-teal-200',
                'dot' => 'bg-teal-600',
                'label' => 'Diterima',
            ],
            'rejected' => [
                'bg' => 'bg-rose-50 text-rose-800 border-rose-200',
                'dot' => 'bg-rose-500',
                'label' => 'Ditolak',
            ],
            default => [
                'bg' => 'bg-slate-100 text-slate-800 border-slate-200',
                'dot' => 'bg-slate-500',
                'label' => ucfirst($this->status),
            ],
        };
    }

    /**
     * Format clean WhatsApp link.
     */
    public function getWhatsappLinkAttribute(): string
    {
        $cleanPhone = preg_replace('/[^0-9]/', '', $this->whatsapp_number);
        if (str_starts_with($cleanPhone, '0')) {
            $cleanPhone = '62' . substr($cleanPhone, 1);
        }
        $greeting = urlencode("Assalamu'alaikum wr. wb. Saudara/i {$this->name}, kami dari Pengurus Pusat KucingMu terkait pendaftaran Penjaringan Representatif Wilayah ({$this->registration_number}).");
        return "https://wa.me/{$cleanPhone}?text={$greeting}";
    }
}
