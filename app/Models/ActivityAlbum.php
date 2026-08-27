<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityAlbum extends Model
{
    use HasFactory;

    protected $table = 'activity_albums';

    protected $fillable = [
        'title',
        'caption',
        'image_path',
        'category',
        'activity_date',
        'order',
        'is_active',
    ];

    protected $casts = [
        'activity_date' => 'date',
        'is_active' => 'boolean',
        'order' => 'integer',
    ];

    /**
     * Scope for active photos ordered by order and activity date.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->orderBy('order', 'asc')
            ->orderBy('activity_date', 'desc')
            ->orderBy('id', 'desc');
    }

    /**
     * Get accessible URL for the album image.
     */
    public function getImageUrlAttribute(): string
    {
        if (empty($this->image_path)) {
            return asset('images/logo-muhammadiyah.svg');
        }

        // Direct URL or Base64
        if (str_starts_with($this->image_path, 'http://') || str_starts_with($this->image_path, 'https://') || str_starts_with($this->image_path, 'data:')) {
            return $this->image_path;
        }

        // Path starting with images/albums/
        if (file_exists(public_path($this->image_path))) {
            return asset($this->image_path);
        }

        // Relative filename inside images/albums/
        if (file_exists(public_path('images/albums/' . $this->image_path))) {
            return asset('images/albums/' . $this->image_path);
        }

        // Storage public fallback
        if (file_exists(storage_path('app/public/' . $this->image_path))) {
            return asset('storage/' . $this->image_path);
        }

        // Return asset path if path already contains images/
        if (str_starts_with($this->image_path, 'images/')) {
            return asset($this->image_path);
        }

        return asset('images/albums/' . $this->image_path);
    }
}
