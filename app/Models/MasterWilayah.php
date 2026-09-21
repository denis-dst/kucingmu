<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class MasterWilayah extends Model
{
    use HasFactory;

    protected $table = 'master_wilayahs';

    protected $fillable = [
        'kode',
        'nama',
        'singkatan',
        'urutan',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'urutan' => 'integer',
    ];

    private const CACHE_KEY_LIST = 'master_wilayah_active_list';
    private const CACHE_KEY_DROPDOWN = 'master_wilayah_dropdown';
    private const CACHE_TTL = 300; // 5 minutes

    /**
     * Default fallback PWM list if table not yet seeded or error occurs.
     */
    public const DEFAULT_FALLBACK_LIST = [
        ['kode' => '34', 'nama' => 'D.I. Yogyakarta (PWM DIY)', 'singkatan' => 'DIY', 'urutan' => 1],
        ['kode' => '33', 'nama' => 'Jawa Tengah (PWM Jateng)', 'singkatan' => 'JATENG', 'urutan' => 2],
        ['kode' => '35', 'nama' => 'Jawa Timur (PWM Jatim)', 'singkatan' => 'JATIM', 'urutan' => 3],
        ['kode' => '31', 'nama' => 'DKI Jakarta (PWM DKI)', 'singkatan' => 'DKI', 'urutan' => 4],
        ['kode' => '32', 'nama' => 'Jawa Barat (PWM Jabar)', 'singkatan' => 'JABAR', 'urutan' => 5],
        ['kode' => '36', 'nama' => 'Banten (PWM Banten)', 'singkatan' => 'BANTEN', 'urutan' => 6],
        ['kode' => '11', 'nama' => 'Aceh (PWM Aceh)', 'singkatan' => 'ACEH', 'urutan' => 7],
        ['kode' => '12', 'nama' => 'Sumatera Utara (PWM Sumut)', 'singkatan' => 'SUMUT', 'urutan' => 8],
        ['kode' => '13', 'nama' => 'Sumatera Barat (PWM Sumbar)', 'singkatan' => 'SUMBAR', 'urutan' => 9],
        ['kode' => '14', 'nama' => 'Riau (PWM Riau)', 'singkatan' => 'RIAU', 'urutan' => 10],
        ['kode' => '15', 'nama' => 'Jambi (PWM Jambi)', 'singkatan' => 'JAMBI', 'urutan' => 11],
        ['kode' => '16', 'nama' => 'Sumatera Selatan (PWM Sumsel)', 'singkatan' => 'SUMSEL', 'urutan' => 12],
        ['kode' => '17', 'nama' => 'Bengkulu (PWM Bengkulu)', 'singkatan' => 'BENGKULU', 'urutan' => 13],
        ['kode' => '18', 'nama' => 'Lampung (PWM Lampung)', 'singkatan' => 'LAMPUNG', 'urutan' => 14],
        ['kode' => '19', 'nama' => 'Kep. Bangka Belitung (PWM Babel)', 'singkatan' => 'BABEL', 'urutan' => 15],
        ['kode' => '21', 'nama' => 'Kepulauan Riau (PWM Kepri)', 'singkatan' => 'KEPRI', 'urutan' => 16],
        ['kode' => '51', 'nama' => 'Bali (PWM Bali)', 'singkatan' => 'BALI', 'urutan' => 17],
        ['kode' => '52', 'nama' => 'Nusa Tenggara Barat (PWM NTB)', 'singkatan' => 'NTB', 'urutan' => 18],
        ['kode' => '53', 'nama' => 'Nusa Tenggara Timur (PWM NTT)', 'singkatan' => 'NTT', 'urutan' => 19],
        ['kode' => '61', 'nama' => 'Kalimantan Barat (PWM Kalbar)', 'singkatan' => 'KALBAR', 'urutan' => 20],
        ['kode' => '62', 'nama' => 'Kalimantan Tengah (PWM Kalteng)', 'singkatan' => 'KALTENG', 'urutan' => 21],
        ['kode' => '63', 'nama' => 'Kalimantan Selatan (PWM Kalsel)', 'singkatan' => 'KALSEL', 'urutan' => 22],
        ['kode' => '64', 'nama' => 'Kalimantan Timur (PWM Kaltim)', 'singkatan' => 'KALTIM', 'urutan' => 23],
        ['kode' => '65', 'nama' => 'Kalimantan Utara (PWM Kaltara)', 'singkatan' => 'KALTARA', 'urutan' => 24],
        ['kode' => '71', 'nama' => 'Sulawesi Utara (PWM Sulut)', 'singkatan' => 'SULUT', 'urutan' => 25],
        ['kode' => '72', 'nama' => 'Sulawesi Tengah (PWM Sulteng)', 'singkatan' => 'SULTENG', 'urutan' => 26],
        ['kode' => '73', 'nama' => 'Sulawesi Selatan (PWM Sulsel)', 'singkatan' => 'SULSEL', 'urutan' => 27],
        ['kode' => '74', 'nama' => 'Sulawesi Tenggara (PWM Sultra)', 'singkatan' => 'SULTRA', 'urutan' => 28],
        ['kode' => '75', 'nama' => 'Gorontalo (PWM Gorontalo)', 'singkatan' => 'GORONTALO', 'urutan' => 29],
        ['kode' => '76', 'nama' => 'Sulawesi Barat (PWM Sulbar)', 'singkatan' => 'SULBAR', 'urutan' => 30],
        ['kode' => '81', 'nama' => 'Maluku (PWM Maluku)', 'singkatan' => 'MALUKU', 'urutan' => 31],
        ['kode' => '82', 'nama' => 'Maluku Utara (PWM Malut)', 'singkatan' => 'MALUT', 'urutan' => 32],
        ['kode' => '91', 'nama' => 'Papua (PWM Papua)', 'singkatan' => 'PAPUA', 'urutan' => 33],
        ['kode' => '92', 'nama' => 'Papua Barat (PWM Papbar)', 'singkatan' => 'PAPBAR', 'urutan' => 34],
        ['kode' => '00', 'nama' => 'Pusat (PP Muhammadiyah)', 'singkatan' => 'PUSAT', 'urutan' => 0],
    ];

    /**
     * Get all active regions ordered by sequence (cached as clean primitive array).
     */
    public static function getActiveList()
    {
        try {
            $arrayData = Cache::remember(self::CACHE_KEY_LIST, self::CACHE_TTL, function () {
                $dbItems = self::where('is_active', true)
                    ->orderBy('urutan', 'asc')
                    ->orderBy('kode', 'asc')
                    ->get(['id', 'kode', 'nama', 'singkatan', 'urutan', 'is_active']);

                if ($dbItems->isEmpty()) {
                    return self::DEFAULT_FALLBACK_LIST;
                }

                return $dbItems->map(fn($item) => [
                    'id' => (int) $item->id,
                    'kode' => (string) $item->kode,
                    'nama' => (string) $item->nama,
                    'singkatan' => (string) ($item->singkatan ?? ''),
                    'urutan' => (int) $item->urutan,
                    'is_active' => (bool) $item->is_active,
                ])->values()->toArray();
            });

            // Guard against corrupted cache data or __PHP_Incomplete_Class
            if (!is_array($arrayData)) {
                self::clearCache();
                $dbItems = self::where('is_active', true)
                    ->orderBy('urutan', 'asc')
                    ->orderBy('kode', 'asc')
                    ->get(['id', 'kode', 'nama', 'singkatan', 'urutan', 'is_active']);

                $arrayData = $dbItems->isNotEmpty()
                    ? $dbItems->map(fn($item) => [
                        'id' => (int) $item->id,
                        'kode' => (string) $item->kode,
                        'nama' => (string) $item->nama,
                        'singkatan' => (string) ($item->singkatan ?? ''),
                        'urutan' => (int) $item->urutan,
                        'is_active' => (bool) $item->is_active,
                    ])->values()->toArray()
                    : self::DEFAULT_FALLBACK_LIST;
            }

            return collect($arrayData)->map(function ($item) {
                if (is_array($item)) {
                    return (object) $item;
                }
                if ($item instanceof self) {
                    return $item;
                }
                if (is_object($item) && !($item instanceof \__PHP_Incomplete_Class) && isset($item->kode)) {
                    return $item;
                }
                return null;
            })->filter(function ($item) {
                return is_object($item) && isset($item->kode) && !str_starts_with((string)$item->kode, '__PHP_');
            })->values();
        } catch (\Throwable $e) {
            self::clearCache();
            return collect(self::DEFAULT_FALLBACK_LIST)->map(fn($item) => (object) $item);
        }
    }

    /**
     * Get array of [kode => nama] for dropdowns (cached).
     */
    public static function getDropdownOptions(): array
    {
        try {
            $options = Cache::remember(self::CACHE_KEY_DROPDOWN, self::CACHE_TTL, function () {
                $dbOptions = self::where('is_active', true)
                    ->orderBy('urutan', 'asc')
                    ->orderBy('kode', 'asc')
                    ->pluck('nama', 'kode')
                    ->toArray();

                if (!empty($dbOptions)) {
                    return $dbOptions;
                }

                $fallback = [];
                foreach (self::DEFAULT_FALLBACK_LIST as $f) {
                    $fallback[$f['kode']] = $f['nama'];
                }
                return $fallback;
            });

            if (!is_array($options) || empty($options)) {
                $fallback = [];
                foreach (self::DEFAULT_FALLBACK_LIST as $f) {
                    $fallback[$f['kode']] = $f['nama'];
                }
                return $fallback;
            }

            return $options;
        } catch (\Throwable $e) {
            $fallback = [];
            foreach (self::DEFAULT_FALLBACK_LIST as $f) {
                $fallback[$f['kode']] = $f['nama'];
            }
            return $fallback;
        }
    }

    /**
     * Clear the wilayah caches.
     */
    public static function clearCache(): void
    {
        try {
            Cache::forget(self::CACHE_KEY_LIST);
            Cache::forget(self::CACHE_KEY_DROPDOWN);
        } catch (\Throwable $e) {
            // Ignore cache clear failures
        }
    }

    /**
     * Cats registered under this wilayah.
     */
    public function cats()
    {
        return $this->hasMany(Cat::class, 'wilayah_code', 'kode');
    }
}
