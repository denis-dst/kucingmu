<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Medicine extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'active_ingredient',
        'dosage_form',
        'concentration_value',
        'concentration_unit',
        'species_scope',
        'prescription_required',
        'is_active',
        'description',
    ];

    protected $casts = [
        'concentration_value' => 'decimal:2',
        'species_scope' => 'array',
        'prescription_required' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function prescriptionItems()
    {
        return $this->hasMany(PrescriptionItem::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
