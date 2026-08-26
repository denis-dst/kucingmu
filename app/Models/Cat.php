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

