<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CatPhoto extends Model
{
    use HasFactory;

    protected $fillable = [
        'cat_id',
        'photo_path',
        'label',
        'is_primary',
        'photo_embedding',
        'color_fingerprint',
        'spatial_fingerprint',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
        'photo_embedding' => 'array',
        'color_fingerprint' => 'array',
        'spatial_fingerprint' => 'array',
    ];

    public function cat()
    {
        return $this->belongsTo(Cat::class);
    }
}
