<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class SocialPostMedia extends Model
{
    use HasFactory;

    protected $table = 'social_post_media';

    protected $fillable = [
        'social_post_id',
        'media_path',
        'media_type',
        'aspect_ratio',
        'sort_order',
    ];

    public function getUrlAttribute(): string
    {
        if (Str::startsWith($this->media_path, 'http')) {
            return $this->media_path;
        }
        return asset('storage/' . $this->media_path);
    }
}
