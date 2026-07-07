<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Cat extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'breed',
        'gender',
        'date_of_birth',
        'photo_path',
        'allergies',
        'vaccine_history',
        'notes',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
    ];

    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    public function medicalRecords()
    {
        return $this->hasMany(MedicalRecord::class);
    }

    public function ktamCard()
    {
        return $this->hasOne(KtamCard::class);
    }
}
