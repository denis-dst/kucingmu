<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CatCondolence extends Model
{
    use HasFactory;

    protected $fillable = [
        'cat_id',
        'user_id',
        'message',
        'prayer_text',
        'is_visible',
    ];

    protected $casts = [
        'is_visible' => 'boolean',
    ];

    public function cat()
    {
        return $this->belongsTo(Cat::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
