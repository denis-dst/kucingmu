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
        'foto_wajah_embedding',
        'multi_embeddings',
        'color_fingerprint',
        'spatial_fingerprint',
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
        'foto_wajah_embedding' => 'array',
        'multi_embeddings' => 'array',
        'color_fingerprint' => 'array',
        'spatial_fingerprint' => 'array',
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
     * Generate the next 3-digit sequential ID for a given PTMA campus (e.g. UMY-001).
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
        while (self::where('id_kucing', $prefix . '-' . str_pad($nextSeq, 3, '0', STR_PAD_LEFT))->exists()) {
            $nextSeq++;
        }

        $idKucing = $prefix . '-' . str_pad($nextSeq, 3, '0', STR_PAD_LEFT);

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

    /**
     * Calculate cosine similarity between two float arrays.
     *
     * @param array $vecA
     * @param array $vecB
     * @return float 0.0 - 1.0
     */
    public static function cosineSimilarity(array $vecA, array $vecB): float
    {
        $count = min(count($vecA), count($vecB));
        if ($count === 0) return 0.0;

        $dotProduct = 0.0;
        $normA = 0.0;
        $normB = 0.0;

        for ($i = 0; $i < $count; $i++) {
            $a = (float) $vecA[$i];
            $b = (float) $vecB[$i];
            $dotProduct += ($a * $b);
            $normA += ($a * $a);
            $normB += ($b * $b);
        }

        if ($normA <= 0.0 || $normB <= 0.0) return 0.0;

        $similarity = $dotProduct / (sqrt($normA) * sqrt($normB));
        return max(0.0, min(1.0, (float) $similarity));
    }

    /**
     * Extract a 64-bin RGB color histogram fingerprint from an image binary string.
     *
     * @param string $binaryData
     * @return array|null
     */
    public static function extractColorFingerprint(string $binaryData): ?array
    {
        $im = @imagecreatefromstring($binaryData);
        if (!$im) return null;

        $w = imagesx($im);
        $h = imagesy($im);

        // Resize to small 64x64 grid for quick sampling
        $thumb = imagecreatetruecolor(64, 64);
        imagecopyresampled($thumb, $im, 0, 0, 0, 0, 64, 64, $w, $h);

        // 4 bins per channel -> 4x4x4 = 64 bins
        $bins = array_fill(0, 64, 0);
        $totalPixels = 64 * 64;

        for ($x = 0; $x < 64; $x++) {
            for ($y = 0; $y < 64; $y++) {
                $rgb = imagecolorat($thumb, $x, $y);
                $r = ($rgb >> 16) & 0xFF;
                $g = ($rgb >> 8) & 0xFF;
                $b = $rgb & 0xFF;

                $rBin = min(3, (int) floor($r / 64));
                $gBin = min(3, (int) floor($g / 64));
                $bBin = min(3, (int) floor($b / 64));

                $binIndex = ($rBin * 16) + ($gBin * 4) + $bBin;
                $bins[$binIndex]++;
            }
        }

        imagedestroy($thumb);
        imagedestroy($im);

        // Normalize
        return array_map(function ($val) use ($totalPixels) {
            return round($val / $totalPixels, 5);
        }, $bins);
    }
}
