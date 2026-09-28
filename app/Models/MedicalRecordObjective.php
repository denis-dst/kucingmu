<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MedicalRecordObjective extends Model
{
    use HasFactory;

    protected $fillable = [
        'medical_record_id',
        'parameter_code',
        'parameter_name',
        'value_numeric',
        'value_text',
        'unit',
        'examination_status',
        'finding',
        'source_type',
        'observed_at',
        'notes',
    ];

    protected $casts = [
        'value_numeric' => 'decimal:2',
        'observed_at' => 'datetime',
    ];

    public function medicalRecord()
    {
        return $this->belongsTo(MedicalRecord::class);
    }
}
