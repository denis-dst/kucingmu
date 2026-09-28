<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MedicalRecordPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'medical_record_id',
        'plan_type',
        'description',
        'priority',
        'planned_at',
        'status',
        'notes',
    ];

    protected $casts = [
        'planned_at' => 'datetime',
    ];

    public function medicalRecord()
    {
        return $this->belongsTo(MedicalRecord::class);
    }
}
