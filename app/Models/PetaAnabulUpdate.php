<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PetaAnabulUpdate extends Model
{
    use HasFactory;

    protected $fillable = [
        'peta_anabul_id',
        'user_id',
        'update_type',
        'notes',
        'condition',
        'status',
        'latitude',
        'longitude',
        'photo_path',
        'moderation_status',
        'moderation_notes',
        'moderated_by',
    ];

    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
    ];

    public function petaAnabul()
    {
        return $this->belongsTo(PetaAnabul::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function moderator()
    {
        return $this->belongsTo(User::class, 'moderated_by');
    }

    /**
     * Human readable update type label.
     */
    public function getTypeLabelAttribute(): string
    {
        return match($this->update_type) {
            'sighting' => 'Penampakan Terbaru',
            'condition_update' => 'Pembaruan Kondisi',
            'handled' => 'Sudah Ditangani / Diselamatkan',
            'not_seen' => 'Tidak Terlihat Lagi',
            'correction' => 'Koreksi Data / Ciri Fisik',
            'duplicate_report' => 'Laporan Duplikat',
            'report_inappropriate' => 'Laporan Konten Tidak Sesuai',
            default => 'Catatan Komunitas',
        };
    }

    /**
     * Badge CSS class for update type.
     */
    public function getTypeBadgeClassAttribute(): string
    {
        return match($this->update_type) {
            'sighting' => 'bg-teal-50 text-teal-700 border-teal-200',
            'condition_update' => 'bg-sky-50 text-sky-700 border-sky-200',
            'handled' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
            'not_seen' => 'bg-slate-100 text-slate-700 border-slate-200',
            'correction' => 'bg-amber-50 text-amber-700 border-amber-200',
            'duplicate_report' => 'bg-orange-50 text-orange-700 border-orange-200',
            'report_inappropriate' => 'bg-rose-50 text-rose-700 border-rose-200',
            default => 'bg-slate-100 text-slate-700 border-slate-200',
        };
    }

    public function getPhotoUrlAttribute(): ?string
    {
        if (empty($this->photo_path)) {
            return null;
        }

        if (str_starts_with($this->photo_path, 'http://') || str_starts_with($this->photo_path, 'https://')) {
            return $this->photo_path;
        }

        return asset('storage/' . $this->photo_path);
    }
}

