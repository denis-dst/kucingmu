<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class PtmaCatCensus extends Model
{
    use HasFactory;

    protected $table = 'ptma_cat_censuses';

    protected $fillable = [
        'volunteer_id',
        'id_kucing',
        'sequence_number',
        'kampus',
        'kampus_custom',
        'zona',
        'latitude',
        'longitude',
        'usia',
        'gender',
        'warna',
        'warna_custom',
        'foto_wajah',
        'foto_atas',
        'foto_samping_kiri',
        'foto_opsional',
        'bcs',
        'kondisi_klinis',
        'panjang_badan_cm',
        'panjang_ekor_cm',
        'jarak_pakan',
        'jenis_pakan',
        'jenis_pakan_custom',
        'ancaman',
        'ancaman_custom',
        'catatan',
    ];

    protected $casts = [
        'kondisi_klinis' => 'array',
        'latitude' => 'float',
        'longitude' => 'float',
        'panjang_badan_cm' => 'float',
        'panjang_ekor_cm' => 'float',
        'jarak_pakan' => 'integer',
        'sequence_number' => 'integer',
    ];

    public function volunteer()
    {
        return $this->belongsTo(User::class, 'volunteer_id');
    }

    /**
     * Generate the next 8-digit sequential ID for a given PTMA campus.
     *
     * @param string $kampus
     * @param string|null $kampusCustom
     * @return array ['id_kucing' => string, 'sequence_number' => int, 'prefix' => string]
     */
    public static function generateNextId(string $kampus, ?string $kampusCustom = null): array
    {
        $prefix = strtoupper(trim($kampus));
        if ($prefix === 'LAINNYA' || empty($prefix)) {
            if (!empty($kampusCustom)) {
                $clean = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $kampusCustom));
                $prefix = substr($clean ?: 'PTMA', 0, 6);
            } else {
                $prefix = 'PTMA';
            }
        }

        // Get the latest sequence for this campus prefix
        $latest = self::where('id_kucing', 'LIKE', $prefix . '-%')
            ->orderBy('sequence_number', 'desc')
            ->first();

        $nextSeq = $latest ? ($latest->sequence_number + 1) : 1;

        // Ensure uniqueness loop in case of manual input
        while (self::where('id_kucing', $prefix . '-' . str_pad($nextSeq, 8, '0', STR_PAD_LEFT))->exists()) {
            $nextSeq++;
        }

        $idKucing = $prefix . '-' . str_pad($nextSeq, 8, '0', STR_PAD_LEFT);

        return [
            'id_kucing' => $idKucing,
            'sequence_number' => $nextSeq,
            'prefix' => $prefix,
        ];
    }

    public function getFotoWajahUrlAttribute(): ?string
    {
        return $this->foto_wajah ? Storage::url($this->foto_wajah) : null;
    }

    public function getFotoAtasUrlAttribute(): ?string
    {
        return $this->foto_atas ? Storage::url($this->foto_atas) : null;
    }

    public function getFotoSampingKiriUrlAttribute(): ?string
    {
        return $this->foto_samping_kiri ? Storage::url($this->foto_samping_kiri) : null;
    }

    public function getFotoOpsionalUrlAttribute(): ?string
    {
        return $this->foto_opsional ? Storage::url($this->foto_opsional) : null;
    }

    public function getDisplayKampusAttribute(): string
    {
        if ($this->kampus === 'Lainnya' && !empty($this->kampus_custom)) {
            return $this->kampus_custom;
        }
        return $this->kampus;
    }

    public function getDisplayWarnaAttribute(): string
    {
        if ($this->warna === 'Lainnya' && !empty($this->warna_custom)) {
            return $this->warna_custom;
        }
        return $this->warna;
    }

    public function getDisplayJenisPakanAttribute(): string
    {
        if ($this->jenis_pakan === 'Lainnya' && !empty($this->jenis_pakan_custom)) {
            return $this->jenis_pakan_custom;
        }
        return $this->jenis_pakan;
    }

    public function getDisplayAncamanAttribute(): string
    {
        if ($this->ancaman === 'Lainnya' && !empty($this->ancaman_custom)) {
            return $this->ancaman_custom;
        }
        return $this->ancaman;
    }
}
