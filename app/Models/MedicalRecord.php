<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MedicalRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'appointment_id',
        'cat_id',
        'vet_id',
        'weight',
        'temperature',
        'general_condition',
        'deworming_given',
        'anti_flea_given',
        'supplement_given',
        'treatment_notes',
        'recommendation',
    ];

    protected $casts = [
        'deworming_given' => 'boolean',
        'anti_flea_given' => 'boolean',
        'supplement_given' => 'boolean',
        'weight' => 'decimal:2',
        'temperature' => 'decimal:1',
    ];

    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }

    public function cat()
    {
        return $this->belongsTo(Cat::class);
    }

    public function vet()
    {
        return $this->belongsTo(User::class, 'vet_id');
    }
}
