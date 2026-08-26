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

    protected static function booted()
    {
        static::creating(function ($cat) {
            if (empty($cat->wilayah_code)) {
                $cat->wilayah_code = '34';
            }
        });

        static::created(function ($cat) {
            if (empty($cat->unique_code)) {
                $cat->unique_code = self::generateUniqueCode($cat->wilayah_code ?? '34', $cat->id);
                $cat->saveQuietly();
            }
        });
    }

    /**
     * Generate unique cat code with format: "kode_wilayah.kcg.xxxx"
     */
    public static function generateUniqueCode(?string $wilayahCode = '34', ?int $sequence = null): string
    {
        $kode = strtolower(trim($wilayahCode ?: '34'));
        $seq = $sequence ?: (self::count() + 1);
        return $kode . '.kcg.' . str_pad($seq, 4, '0', STR_PAD_LEFT);
    }

    public function getFormattedUniqueCodeAttribute(): string
    {
        if (!empty($this->unique_code)) {
            return $this->unique_code;
        }
        return self::generateUniqueCode($this->wilayah_code ?? '34', $this->id);
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

