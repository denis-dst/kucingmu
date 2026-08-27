<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
     *
     * @return array<string>
     */
    public static function getAllBreedNames(): array
    {
        try {
            $dbBreeds = self::orderBy('is_default', 'desc')
                ->orderBy('order', 'asc')
                ->orderBy('name', 'asc')
                ->pluck('name')
                ->toArray();

            if (!empty($dbBreeds)) {
                // Ensure all default breeds are present in the list
                $merged = array_unique(array_merge(self::DEFAULT_BREEDS, $dbBreeds));
                return array_values($merged);
            }
        } catch (\Throwable $e) {
            // Fallback if table does not exist yet
        }

        return self::DEFAULT_BREEDS;
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

            return self::create([
                'name' => $formattedName,
                'is_default' => $isDefault,
                'order' => $isDefault ? (array_search($formattedName, self::DEFAULT_BREEDS) + 1) : 99,
            ]);
        } catch (\Throwable $e) {
            return null;
        }
    }
}
