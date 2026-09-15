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
     * Get all active regions ordered by sequence (cached).
     */
    public static function getActiveList()
    {
        return Cache::remember(self::CACHE_KEY_LIST, self::CACHE_TTL, function () {
            return self::where('is_active', true)->orderBy('urutan')->orderBy('kode')->get();
        });
    }

    /**
     * Get array of [kode => nama] for dropdowns (cached).
     */
    public static function getDropdownOptions(): array
    {
        return Cache::remember(self::CACHE_KEY_DROPDOWN, self::CACHE_TTL, function () {
            return self::where('is_active', true)
                ->orderBy('urutan')
                ->orderBy('kode')
                ->pluck('nama', 'kode')
                ->toArray();
        });
    }

    /**
     * Clear the wilayah caches.
     */
    public static function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY_LIST);
        Cache::forget(self::CACHE_KEY_DROPDOWN);
    }

    /**
     * Cats registered under this wilayah.
     */
    public function cats()
    {
        return $this->hasMany(Cat::class, 'wilayah_code', 'kode');
    }
}
