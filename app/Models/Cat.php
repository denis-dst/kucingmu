<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Cat extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'breed',
        'gender',
        'status',
        'date_of_birth',
        'wilayah_code',
        'unique_code',
        'color',
        'photo_path',
        'allergies',
        'vaccine_history',
        'notes',
        'biometric_type',
        'biometric_photo_path',
        'biometric_code',
        'photo_embedding',
        'color_fingerprint',
        'spatial_fingerprint',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'photo_embedding' => 'array',
        'color_fingerprint' => 'array',
        'spatial_fingerprint' => 'array',
    ];

    /**
     * Check if cat is currently alive.
     */
    public function isAlive(): bool
    {
        return empty($this->status) || in_array(strtolower(trim($this->status)), ['alive', 'hidup']);
    }

    /**
     * Check if cat is deceased.
     */
    public function isDeceased(): bool
    {
        return !$this->isAlive();
    }

    /**
     * Human readable status label.
     */
    public function getStatusLabelAttribute(): string
    {
        return $this->isAlive() ? 'Hidup' : 'Mati';
    }

    /**
     * Human readable age text (e.g. "1 thn 3 bln" or "4 bln").
     */
    public function getAgeTextAttribute(): string
    {
        if (!$this->date_of_birth) {
            return '-';
        }

        $now = now();
        $years = (int) $this->date_of_birth->diffInYears($now);
        $months = (int) ($this->date_of_birth->diffInMonths($now) % 12);

        if ($years > 0) {
            return $years . ' thn' . ($months > 0 ? ' ' . $months . ' bln' : '');
        }

        $totalMonths = (int) $this->date_of_birth->diffInMonths($now);
        if ($totalMonths > 0) {
            return $totalMonths . ' bln';
        }

        $days = (int) $this->date_of_birth->diffInDays($now);
        return $days . ' hari';
    }

    protected static function booted()
    {
        static::created(function ($cat) {
            if (!empty($cat->wilayah_code) && empty($cat->unique_code)) {
                $cat->unique_code = self::generateUniqueCode($cat->wilayah_code, $cat->id);
                $cat->saveQuietly();
            }
        });

        static::updating(function ($cat) {
            if ($cat->isDirty('wilayah_code')) {
                if (!empty($cat->wilayah_code)) {
                    $cat->unique_code = self::generateUniqueCode($cat->wilayah_code, $cat->id);
                } else {
                    $cat->unique_code = null;
                }

                // Synchronize ktam_cards table if card exists
                if ($cat->ktamCard) {
                    if (!empty($cat->unique_code)) {
                        $cat->ktamCard->update(['ktam_number' => $cat->unique_code]);
                    }
                }
            }
        });
    }

    /**
     * Generate unique cat code with format: "kode_wilayah.kcg.00xx"
     */
    public static function generateUniqueCode(?string $wilayahCode = null, ?int $id = null): ?string
    {
        if (empty($wilayahCode)) {
            return null;
        }

        $kode = strtolower(trim($wilayahCode));
        $seq = $id ?: 1;
        return $kode . '.kcg.' . str_pad($seq, 4, '0', STR_PAD_LEFT);
    }

    public function getFormattedUniqueCodeAttribute(): string
    {
        if (!empty($this->unique_code)) {
            return $this->unique_code;
        }
        if (!empty($this->wilayah_code)) {
            return self::generateUniqueCode($this->wilayah_code, $this->id);
        }
        return '-';
    }

    public function wilayah()
    {
        return $this->belongsTo(MasterWilayah::class, 'wilayah_code', 'kode');
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    public function medicalRecords()
    {
        return $this->hasMany(MedicalRecord::class);
    }

    public function ktamCard()
    {
        return $this->hasOne(KtamCard::class);
    }

    public function photos()
    {
        return $this->hasMany(CatPhoto::class);
    }

    public function primaryPhoto()
    {
        return $this->hasOne(CatPhoto::class)->where('is_primary', true);
    }

    public function getPrimaryPhotoUrlAttribute()
    {
        $primary = $this->photos ? $this->photos->firstWhere('is_primary', true) : null;
        if ($primary && $primary->photo_path) {
            $fullPath = storage_path('app/public/' . $primary->photo_path);
            if (file_exists($fullPath)) {
                return asset('storage/' . $primary->photo_path);
            }
        }

        if ($this->photo_path) {
            $fullPath = storage_path('app/public/' . $this->photo_path);
            if (file_exists($fullPath)) {
                return asset('storage/' . $this->photo_path);
            }
        }

        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&color=0F766E&background=E6F4F1&size=128';
    }

    public function getPrimaryPhotoPathAttribute()
    {
        $primary = $this->photos->firstWhere('is_primary', true);
        if ($primary && $primary->photo_path) {
            return $primary->photo_path;
        }

        return $this->photo_path;
    }
}

