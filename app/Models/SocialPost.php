<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SocialPost extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'category',
        'caption',
        'tagged_cat_id',
        'location',
        'likes_count',
        'comments_count',
        'is_active',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function taggedCat()
    {
        return $this->belongsTo(Cat::class, 'tagged_cat_id');
    }

    public function media()
    {
        return $this->hasMany(SocialPostMedia::class)->orderBy('sort_order', 'asc');
    }

    public function comments()
    {
        return $this->hasMany(SocialComment::class)->latest();
    }

    public function likes()
    {
        return $this->hasMany(SocialLike::class);
    }
}
