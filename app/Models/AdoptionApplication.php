<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdoptionApplication extends Model
{
    use HasFactory;

    protected $table = 'adoption_applications';

    protected $fillable = [
        'application_code',
        'user_id',
        'cat_id',
        'stray_cat_survey_id',
        'cat_source',
        'cat_name',
        'applicant_name',
        'applicant_email',
        'applicant_phone',
        'applicant_city',
        'applicant_address',
        'housing_type',
        'has_other_pets',
        'has_family_consent',
        'commitment_notes',
        'status',
        'admin_notes',
        'reviewed_by',
        'reviewed_at',
    ];

    protected $casts = [
        'has_family_consent' => 'boolean',
        'reviewed_at' => 'datetime',
    ];

    /**
     * Generate unique application code (e.g. ADP-202609-0001).
     */
    public static function generateCode(): string
    {
        $prefix = 'ADP-' . date('Ym') . '-';
        $last = self::where('application_code', 'like', "{$prefix}%")
            ->orderBy('id', 'desc')
            ->first();

        if ($last) {
            $lastSeq = (int) substr($last->application_code, -4);
            $seq = $lastSeq + 1;
        } else {
            $seq = 1;
        }

        return $prefix . str_pad($seq, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Applicant user account if registered.
     */
    public function applicant(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Cat if source is member cat.
     */
    public function cat(): BelongsTo
    {
        return $this->belongsTo(Cat::class, 'cat_id')->withTrashed();
    }

    /**
     * Stray survey if source is rescue/survey.
     */
    public function strayCatSurvey(): BelongsTo
    {
        return $this->belongsTo(StrayCatSurvey::class, 'stray_cat_survey_id');
    }

    /**
     * Admin/volunteer reviewer.
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
            'pending' => 'Menunggu Verifikasi Admin',
            'reviewing' => 'Sedang Ditinjau Admin',
            'approved' => 'Disetujui (Siap Temu/Adopsi)',
            'rejected' => 'Tidak Disetujui',
            'completed' => 'Adopsi Selesai (Resmi Diadopsi)',
            'cancelled' => 'Dibatalkan',
            default => ucfirst($this->status),
        };
    }

    /**
     * Status badge styling.
     */
    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'bg-amber-50 text-amber-800 border-amber-200',
            'reviewing' => 'bg-blue-50 text-blue-800 border-blue-200',
            'approved' => 'bg-emerald-50 text-emerald-800 border-emerald-200',
            'rejected' => 'bg-rose-50 text-rose-800 border-rose-200',
            'completed' => 'bg-purple-50 text-purple-800 border-purple-200',
            'cancelled' => 'bg-slate-100 text-slate-600 border-slate-200',
            default => 'bg-slate-100 text-slate-700 border-slate-200',
        };
    }

    /**
     * Cat source label.
     */
    public function getCatSourceLabelAttribute(): string
    {
        return $this->cat_source === 'stray_survey' ? '📋 Kucing Sensus / Rescue' : '🐱 Kucing dari Member';
    }

    /**
     * Masked applicant phone number for public/semi-public display.
     */
    public function getMaskedPhoneAttribute(): string
    {
        $phone = preg_replace('/[^0-9]/', '', $this->applicant_phone ?? '');
        if (strlen($phone) < 8) {
            return '08*** (Disamarkan)';
        }
        return substr($phone, 0, 4) . '****' . substr($phone, -3);
    }

    /**
     * WhatsApp URL for Admin to directly contact the applicant.
     */
    public function getAdminWhatsAppUrlAttribute(): string
    {
        $phone = preg_replace('/[^0-9]/', '', $this->applicant_phone ?? '');
        if (str_starts_with($phone, '0')) {
            $phone = '62' . substr($phone, 1);
        }

        $catName = $this->cat_name;
        $appCode = $this->application_code;
        $msg = rawurlencode("Halo Kak {$this->applicant_name}, kami dari Tim Pengelola KucingMu mengonfirmasi pengajuan adopsi anabul {$catName} (Kode: {$appCode}). Apakah ada waktu luang untuk berdiskusi?");

        return "https://wa.me/{$phone}?text={$msg}";
    }
}
