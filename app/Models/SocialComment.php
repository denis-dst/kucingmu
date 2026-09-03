<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SocialComment extends Model
{
    use HasFactory;

    protected $fillable = [
        'social_post_id',
        'user_id',
        'comment',
        'is_vet_verified',
        'likes_count',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function post()
    {
        return $this->belongsTo(SocialPost::class, 'social_post_id');
    }
}
