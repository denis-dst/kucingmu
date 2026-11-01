<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TemuAnabulSighting extends Model
{
    use HasFactory;

    protected $fillable = [
        'temu_anabul_id',
        'user_id',
        'notes',
        'sighting_time',
        'latitude',
        'longitude',
        'location_name',
        'photo_path',
        'contact_phone',
    ];

    protected $casts = [
        'sighting_time' => 'datetime',
        'latitude' => 'float',
        'longitude' => 'float',
    ];

    public function temuAnabul()
    {
        return $this->belongsTo(TemuAnabul::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
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

