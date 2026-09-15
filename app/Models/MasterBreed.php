<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class MasterBreed extends Model
{
    use HasFactory;

    protected $table = 'master_breeds';

    protected $fillable = [
        'name',
        'is_default',
        'order',
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'order' => 'integer',
    ];

    private const CACHE_KEY = 'master_breeds_all';
    private const CACHE_TTL = 300; // 5 minutes

    /**
     * Default 10 Master Breeds as required.
     */
    public const DEFAULT_BREEDS = [
        'Anggora',
        'Persia',
        'Bengal',
        'Sphynx',
        'Maine Coon',
        'Ragdoll',
        'Siamese',
        'British Shorthair',
        'Domestik',
        'Mixdom',
    ];

    /**
     * Get all breeds list sorted: default breeds first, then custom breeds alphabetically.
     * Cached for 5 minutes to avoid redundant DB queries.
     *
     * @return array<string>
     */
    public static function getAllBreedNames(): array
    {
        try {
            return Cache::remember(self::CACHE_KEY, self::CACHE_TTL, function () {
                $dbBreeds = self::orderBy('is_default', 'desc')
                    ->orderBy('order', 'asc')
                    ->orderBy('name', 'asc')
                    ->pluck('name')
                    ->toArray();

                if (!empty($dbBreeds)) {
                    $merged = array_unique(array_merge(self::DEFAULT_BREEDS, $dbBreeds));
                    return array_values($merged);
                }

                return self::DEFAULT_BREEDS;
            });
        } catch (\Throwable $e) {
            return self::DEFAULT_BREEDS;
        }
    }

    /**
     * Clear the breeds cache.
     */
    public static function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    /**
     * Automatically register a new breed if it does not already exist.
     *
     * @param string|null $breedName
     * @return self|null
     */
    public static function registerBreedIfNotExists(?string $breedName): ?self
    {
        if (empty($breedName)) {
            return null;
        }

        $cleanName = trim(preg_replace('/\s+/', ' ', $breedName));
        if (empty($cleanName) || strcasecmp($cleanName, 'lainnya') === 0 || strcasecmp($cleanName, 'other') === 0) {
            return null;
        }

        // Capitalize words nicely (e.g. "kampung liar" -> "Kampung Liar")
        $formattedName = ucwords(strtolower($cleanName));

        try {
            $existing = self::whereRaw('LOWER(name) = ?', [strtolower($cleanName)])->first();
            if ($existing) {
                return $existing;
            }

            $isDefault = in_array($formattedName, self::DEFAULT_BREEDS);

            $breed = self::create([
                'name' => $formattedName,
                'is_default' => $isDefault,
                'order' => $isDefault ? (array_search($formattedName, self::DEFAULT_BREEDS) + 1) : 99,
            ]);

            // Invalidate cache when new breed is added
            self::clearCache();

            return $breed;
        } catch (\Throwable $e) {
            return null;
        }
    }
}
