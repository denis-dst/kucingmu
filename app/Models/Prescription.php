<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Prescription extends Model
{
    use HasFactory;

    protected $fillable = [
        'prescription_number',
        'medical_record_id',
        'prescribed_by',
        'status',
        'issued_at',
        'cancelled_at',
        'cancellation_reason',
        'replaces_prescription_id',
    ];

    protected $casts = [
        'issued_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    public function medicalRecord()
    {
        return $this->belongsTo(MedicalRecord::class);
    }

    public function doctor()
    {
        return $this->belongsTo(User::class, 'prescribed_by');
    }

    public function items()
    {
        return $this->hasMany(PrescriptionItem::class);
    }

    public function replaces()
    {
        return $this->belongsTo(Prescription::class, 'replaces_prescription_id');
    }

    public function replacedBy()
    {
        return $this->hasOne(Prescription::class, 'replaces_prescription_id');
    }

    public function isIssued(): bool
    {
        return $this->status === 'issued';
    }

    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    public static function generatePrescriptionNumber(): string
    {
        $prefix = 'RX-' . date('Ym') . '-';
        $latest = self::where('prescription_number', 'like', $prefix . '%')
            ->orderBy('id', 'desc')
            ->first();

        if ($latest && preg_match('/RX-\d{6}-(\d+)/', $latest->prescription_number, $matches)) {
            $seq = intval($matches[1]) + 1;
        } else {
            $seq = 1;
        }

        return $prefix . str_pad($seq, 4, '0', STR_PAD_LEFT);
    }
}
