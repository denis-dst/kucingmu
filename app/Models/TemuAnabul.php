<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TemuAnabul extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'cat_id',
        'type',
        'status',
        'title',
        'cat_name',
        'gender',
        'age_estimate',
        'coat_pattern',
        'coat_color',
        'distinctive_features',
        'condition',
        'incident_date',
        'latitude',
        'longitude',
        'search_radius_km',
        'province',
        'city',
        'district',
        'village',
        'formatted_address',
        'contact_name',
        'contact_phone',
        'is_contact_public',
        'reward_info',
        'holding_status',
        'notes',
        'reunited_at',
        'reunion_notes',
        'reunion_photo_path',
    ];

    protected $casts = [
        'incident_date' => 'datetime',
        'reunited_at' => 'datetime',
        'latitude' => 'float',
        'longitude' => 'float',
        'search_radius_km' => 'float',
        'is_contact_public' => 'boolean',
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
        return $this->hasMany(TemuAnabulPhoto::class)->orderBy('is_primary', 'desc')->orderBy('id', 'asc');
    }

    public function primaryPhoto()
    {
        return $this->hasOne(TemuAnabulPhoto::class)->ofMany([
            'is_primary' => 'max',
            'id' => 'min',
        ]);
    }

    public function sightings()
    {
        return $this->hasMany(TemuAnabulSighting::class)->latest();
    }

    public function isLost(): bool
    {
        return $this->type === 'lost';
    }

    public function isFound(): bool
    {
        return $this->type === 'found';
    }

    public function isReunited(): bool
    {
        return $this->status === 'reunited';
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Type label.
     */
    public function getTypeLabelAttribute(): string
    {
        return $this->isLost() ? 'Kucing Hilang' : 'Kucing Ditemukan';
    }

    /**
     * Type badge CSS classes.
     */
    public function getTypeBadgeClassAttribute(): string
    {
        if ($this->isReunited()) {
            return 'bg-emerald-50 text-emerald-700 border-emerald-200';
        }

        return $this->isLost()
            ? 'bg-rose-50 text-rose-700 border-rose-200'
            : 'bg-indigo-50 text-indigo-700 border-indigo-200';
    }

    /**
     * Status label attribute.
     */
    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'active' => $this->isLost() ? 'Dalam Pencarian' : 'Menunggu Pemilik',
            'reunited' => 'Sudah Ditemukan / Reuni',
            'closed' => 'Laporan Ditutup',
            default => 'Aktif',
        };
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
     * WhatsApp click-to-chat URL.
     */
    public function getWhatsappUrlAttribute(): ?string
    {
        if (empty($this->contact_phone)) {
            return null;
        }

        $phone = preg_replace('/[^0-9]/', '', $this->contact_phone);
        if (str_starts_with($phone, '0')) {
            $phone = '62' . substr($phone, 1);
        }

        $text = rawurlencode("Halo {$this->contact_name}, saya melihat laporan KucingMU untuk: {$this->cat_name} ({$this->type_label}) di {$this->city}.");
        return "https://wa.me/{$phone}?text={$text}";
    }

    /**
     * Smart matching candidate reports.
     * If this is 'lost', search 'found' reports with similar color/pattern/gender in same regency/city or within 30km.
     */
    public function getPotentialMatches(int $limit = 5)
    {
        $oppositeType = $this->isLost() ? 'found' : 'lost';

        $query = self::with(['photos', 'user'])
            ->where('type', $oppositeType)
            ->where('status', 'active')
            ->where('id', '!=', $this->id);

        if ($this->city) {
            $query->where(function ($q) {
                $q->where('city', $this->city)
                  ->orWhere('province', $this->province);
            });
        }

        // Prefer same gender or unknown
        if ($this->gender !== 'tidak_diketahui') {
            $query->whereIn('gender', [$this->gender, 'tidak_diketahui']);
        }

        return $query->latest('incident_date')->take($limit)->get();
    }

    /**
     * Leaflet Pin Marker Visual Configuration.
     */
    public function getPinVisualAttribute(): array
    {
        if ($this->isReunited()) {
            return [
                'type' => 'reunited',
                'bg_color' => '#059669',     // Emerald
                'border_color' => '#065f46',
                'icon' => 'check',
                'label' => 'Sudah Ditemukan',
            ];
        }

        if ($this->isLost()) {
            return [
                'type' => 'lost',
                'bg_color' => '#e11d48',     // Rose / Red
                'border_color' => '#9f1239',
                'icon' => 'search',
                'label' => 'Kucing Hilang',
            ];
        }

        return [
            'type' => 'found',
            'bg_color' => '#7c3aed',         // Violet / Indigo
            'border_color' => '#5b21b6',
            'icon' => 'sparkles',
            'label' => 'Kucing Ditemukan',
        ];
    }

    public function canEditBy(?User $user): bool
    {
        if (!$user) return false;
        if ($user->hasRole('admin', 'superadmin')) return true;
        return $this->user_id === $user->id;
    }
}

