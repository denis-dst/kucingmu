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
        if (empty($this->media_path)) {
            return 'https://images.unsplash.com/photo-1514888286974-6c03e2ca1dba?w=600';
        }

        if (Str::startsWith($this->media_path, ['http://', 'https://'])) {
            return $this->media_path;
        }

        // Check if legacy broken local device path was saved
        if (Str::startsWith($this->media_path, ['/data/', 'C:\\', 'D:\\', '/storage/emulated/'])) {
            return 'https://images.unsplash.com/photo-1514888286974-6c03e2ca1dba?w=600';
        }

        return asset('storage/' . ltrim($this->media_path, '/'));
    }
}
