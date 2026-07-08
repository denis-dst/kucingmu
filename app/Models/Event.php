<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'date',
        'location',
        'registration_link',
        'banner_path',
        'status',
    ];

    protected $casts = [
        'date' => 'date',
    ];
}
