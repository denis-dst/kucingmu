<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MedicalRecordDiagnosis extends Model
{
    use HasFactory;

    protected $table = 'medical_record_diagnoses';

    protected $fillable = [
        'medical_record_id',
        'diagnosis_code',
        'diagnosis_name',
        'diagnosis_type',
        'certainty',
        'severity',
        'clinical_reasoning',
        'is_primary',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
    ];

    public function medicalRecord()
    {
        return $this->belongsTo(MedicalRecord::class);
    }
}
