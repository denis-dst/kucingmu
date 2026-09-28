<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MedicalRecordFollowup extends Model
{
    use HasFactory;

    protected $fillable = [
        'medical_record_id',
        'followup_type',
        'scheduled_at',
        'reason',
        'status',
        'followup_record_id',
        'outcome_notes',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
    ];

    public function medicalRecord()
    {
        return $this->belongsTo(MedicalRecord::class);
    }

    public function followupRecord()
    {
        return $this->belongsTo(MedicalRecord::class, 'followup_record_id');
    }
}
