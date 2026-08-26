<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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

    /**
     * Get all active regions ordered by sequence.
     */
    public static function getActiveList()
    {
        return self::where('is_active', true)->orderBy('urutan')->orderBy('kode')->get();
    }

    /**
     * Get array of [kode => nama] for dropdowns.
     */
    public static function getDropdownOptions(): array
    {
        return self::where('is_active', true)
            ->orderBy('urutan')
            ->orderBy('kode')
            ->pluck('nama', 'kode')
            ->toArray();
    }

    /**
     * Cats registered under this wilayah.
     */
    public function cats()
    {
        return $this->hasMany(Cat::class, 'wilayah_code', 'kode');
    }
}
