<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class PetaAnabul extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'cat_id',
        'ownership_type',
        'name',
        'unique_code',
        'coat_pattern',
        'coat_color',
        'gender',
        'age_estimate',
        'characteristics',
        'condition',
        'sterilization_status',
        'status',
        'latitude',
        'longitude',
        'province',
        'city',
        'district',
        'village',
        'formatted_address',
        'last_seen_at',
        'description',
        'is_verified',
        'moderation_status',
    ];

    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
        'last_seen_at' => 'datetime',
        'is_verified' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function cat()
    {
        return $this->belongsTo(Cat::class);
    }

    public function photos()
    {
        return $this->hasMany(PetaAnabulPhoto::class)->orderBy('is_primary', 'desc')->orderBy('id', 'asc');
    }

    public function primaryPhoto()
    {
        return $this->hasOne(PetaAnabulPhoto::class)->ofMany([
            'is_primary' => 'max',
            'id' => 'min',
        ]);
    }

    public function updates()
    {
        return $this->hasMany(PetaAnabulUpdate::class)->latest();
    }

    /**
     * Primary thumbnail photo URL.
     */
    public function getThumbnailUrlAttribute(): string
    {
        $primary = $this->photos->firstWhere('is_primary', true) ?? $this->photos->first();
        if ($primary) {
            return $primary->url;
        }

        if ($this->cat && $this->cat->photo_path) {
            return asset('storage/' . $this->cat->photo_path);
        }

        return asset('images/default-cat.png');
    }

    /**
     * Check if cat is member pet cat.
     */
    public function isPet(): bool
    {
        return $this->ownership_type === 'pet';
    }

    /**
     * Check if cat is stray / community cat.
     */
    public function isStray(): bool
    {
        return $this->ownership_type === 'stray';
    }

    /**
     * Label for ownership type.
     */
    public function getOwnershipLabelAttribute(): string
    {
        return $this->isPet() ? 'Kucing Peliharaan' : 'Kucing Liar / Sekitar';
    }

    /**
     * Condition text label.
     */
    public function getConditionLabelAttribute(): string
    {
        return match($this->condition) {
            'sehat' => 'Sehat',
            'sakit' => 'Sakit',
            'terluka' => 'Terluka',
            'butuh_pertolongan' => 'Butuh Pertolongan Segera',
            default => 'Tidak Diketahui',
        };
    }

    /**
     * Condition badge styling.
     */
    public function getConditionBadgeClassAttribute(): string
    {
        return match($this->condition) {
            'sehat' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
            'sakit' => 'bg-rose-50 text-rose-700 border-rose-200',
            'terluka' => 'bg-red-50 text-red-800 border-red-300 animate-pulse',
            'butuh_pertolongan' => 'bg-amber-50 text-amber-800 border-amber-300 font-bold',
            default => 'bg-slate-100 text-slate-700 border-slate-200',
        };
    }

    /**
     * Status label attribute.
     */
    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'aktif' => 'Aktif di Lokasi',
            'sudah_ditangani' => 'Sudah Ditangani / Dirawat',
            'tidak_terlihat_lagi' => 'Tidak Terlihat Lagi',
            'arsip' => 'Diarsipkan',
            default => 'Aktif',
        };
    }

    /**
     * Pin Marker Visual Configuration for Leaflet.
     * Differentiates between member pet cat vs stray cat in surrounding area.
     */
    public function getPinVisualAttribute(): array
    {
        if ($this->isPet()) {
            return [
                'type' => 'pet',
                'bg_color' => '#0f766e',      // Teal primary
                'border_color' => '#134e4a',  // Dark teal
                'accent_color' => '#2dd4bf',  // Light teal
                'icon' => 'home',             // Pet symbol
                'label' => 'Kucing Peliharaan Member',
                'badge' => 'Peliharaan',
            ];
        }

        return [
            'type' => 'stray',
            'bg_color' => '#ea580c',          // Orange / Amber
            'border_color' => '#9a3412',      // Dark rust
            'accent_color' => '#fed7aa',      // Light orange
            'icon' => 'paw',                  // Stray paw symbol
            'label' => 'Kucing Liar Sekitar',
            'badge' => 'Kucing Liar',
        ];
    }

    /**
     * Generate unique identification code (PA-YYYYMM-XXXXX).
     */
    public static function generateUniqueCode(): string
    {
        $prefix = 'PA-' . date('Ym') . '-';
        $random = strtoupper(Str::random(5));
        
        while (self::where('unique_code', $prefix . $random)->exists()) {
            $random = strtoupper(Str::random(5));
        }

        return $prefix . $random;
    }

    /**
     * Check if a given user can edit this entry.
     */
    public function canEditBy(?User $user): bool
    {
        if (!$user) return false;
        if ($user->hasRole('admin', 'superadmin')) return true;
        return $this->user_id === $user->id;
    }
}

