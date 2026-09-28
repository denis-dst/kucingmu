<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MedicalRecordSubjective extends Model
{
    use HasFactory;

    protected $fillable = [
        'medical_record_id',
        'member_complaint',
        'symptom_onset',
        'symptom_history',
        'appetite_history',
        'drinking_history',
        'urination_history',
        'defecation_history',
        'medication_history',
        'allergy_history',
        'vaccination_history',
        'doctor_clarification',
        'additional_notes',
    ];

    public function medicalRecord()
    {
        return $this->belongsTo(MedicalRecord::class);
    }
}
