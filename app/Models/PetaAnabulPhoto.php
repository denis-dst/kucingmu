<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class PetaAnabulPhoto extends Model
{
    use HasFactory;

    protected $fillable = [
        'peta_anabul_id',
        'photo_path',
        'caption',
        'is_primary',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
    ];

    public function petaAnabul()
    {
        return $this->belongsTo(PetaAnabul::class);
    }

    /**
     * Get accessible URL for this photo.
     */
    public function getUrlAttribute(): string
    {
        if (empty($this->photo_path)) {
            return asset('images/default-cat.png');
        }

        if (str_starts_with($this->photo_path, 'http://') || str_starts_with($this->photo_path, 'https://')) {
            return $this->photo_path;
        }

        return asset('storage/' . $this->photo_path);
    }
}

