<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Appointment extends Model
{
    use HasFactory;

    protected $fillable = [
        'cat_id',
        'date',
        'time_slot',
        'status',
        'notes',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function cat()
    {
        return $this->belongsTo(Cat::class);
    }

    public function medicalRecord()
    {
        return $this->hasOne(MedicalRecord::class);
    }
}
