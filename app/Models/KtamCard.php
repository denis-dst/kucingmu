<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class KtamCard extends Model
{
    use HasFactory;

    protected $fillable = [
        'cat_id',
        'ktam_number',
        'issue_date',
        'qr_code_payload',
        'is_printed',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'is_printed' => 'boolean',
    ];

    public function cat()
    {
        return $this->belongsTo(Cat::class);
    }
}
