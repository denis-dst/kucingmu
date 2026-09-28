<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PrescriptionItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'prescription_id',
        'medicine_id',
        'medicine_name_snapshot',
        'active_ingredient',
        'dosage_form',
        'concentration_value',
        'concentration_unit',
        'dose_value',
        'dose_unit',
        'dose_basis',
        'administration_route',
        'frequency_value',
        'frequency_unit',
        'duration_value',
        'duration_unit',
        'quantity_value',
        'quantity_unit',
        'usage_instructions',
        'warnings',
    ];

    protected $casts = [
        'concentration_value' => 'decimal:2',
        'dose_value' => 'decimal:2',
        'quantity_value' => 'decimal:2',
    ];

    public function prescription()
    {
        return $this->belongsTo(Prescription::class);
    }

    public function medicine()
    {
        return $this->belongsTo(Medicine::class);
    }
}
