<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MedicalRecordAdvice extends Model
{
    use HasFactory;

    protected $table = 'medical_record_advices';

    protected $fillable = [
        'medical_record_id',
        'category',
        'title',
        'instruction',
        'urgency',
        'visible_to_member',
    ];

    protected $casts = [
        'visible_to_member' => 'boolean',
    ];

    public function medicalRecord()
    {
        return $this->belongsTo(MedicalRecord::class);
    }
}
