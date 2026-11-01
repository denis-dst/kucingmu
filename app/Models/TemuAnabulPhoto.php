<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TemuAnabulPhoto extends Model
{
    use HasFactory;

    protected $fillable = [
        'temu_anabul_id',
        'photo_path',
        'is_primary',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
    ];

    public function temuAnabul()
    {
        return $this->belongsTo(TemuAnabul::class);
    }

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

