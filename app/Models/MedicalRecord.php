<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MedicalRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'record_number',
        'appointment_id',
        'cat_id',
        'member_id',
        'vet_id',
        'service_type',
        'clinic_name',
        'status',
        'chief_complaint',
        'weight',
        'temperature',
        'general_condition',
        'deworming_given',
        'anti_flea_given',
        'supplement_given',
        'treatment_notes',
        'recommendation',
        'internal_notes',
        'started_at',
        'completed_at',
        'created_by',
    ];

    protected $casts = [
        'deworming_given' => 'boolean',
        'anti_flea_given' => 'boolean',
        'supplement_given' => 'boolean',
        'weight' => 'decimal:2',
        'temperature' => 'decimal:1',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    // Status Constants
    public const STATUS_DRAFT = 'draft';
    public const STATUS_IN_PROGRESS = 'in_progress';
    public const STATUS_AWAITING_TESTS = 'awaiting_tests';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_CANCELLED = 'cancelled';

    // Service Types
    public const SERVICE_CLINIC = 'clinic';
    public const SERVICE_ONLINE = 'online';

    /* -------------------------------------------------------------------------
     * Relationships
     * ------------------------------------------------------------------------- */

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }

    public function cat(): BelongsTo
    {
        return $this->belongsTo(Cat::class);
    }

    public function vet(): BelongsTo
    {
        return $this->belongsTo(User::class, 'vet_id');
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'vet_id');
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(User::class, 'member_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function subjective(): HasOne
    {
        return $this->hasOne(MedicalRecordSubjective::class);
    }

    public function objectives(): HasMany
    {
        return $this->hasMany(MedicalRecordObjective::class);
    }

    public function diagnoses(): HasMany
    {
        return $this->hasMany(MedicalRecordDiagnosis::class);
    }

    public function primaryDiagnosis(): HasOne
    {
        return $this->hasOne(MedicalRecordDiagnosis::class)->where('is_primary', true);
    }

    public function plans(): HasMany
    {
        return $this->hasMany(MedicalRecordPlan::class);
    }

    public function prescriptions(): HasMany
    {
        return $this->hasMany(Prescription::class);
    }

    public function advices(): HasMany
    {
        return $this->hasMany(MedicalRecordAdvice::class);
    }

    public function followups(): HasMany
    {
        return $this->hasMany(MedicalRecordFollowup::class);
    }

    public function auditLogs(): HasMany
    {
        return $this->hasMany(MedicalRecordAuditLog::class)->orderBy('occurred_at', 'desc');
    }

    /* -------------------------------------------------------------------------
     * Helper Methods & Attributes
     * ------------------------------------------------------------------------- */

    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    public function isDraft(): bool
    {
        return in_array($this->status, [self::STATUS_DRAFT, self::STATUS_IN_PROGRESS]);
    }

    public function isCancelled(): bool
    {
        return $this->status === self::STATUS_CANCELLED;
    }

    public function canBeEditedBy(?User $user): bool
    {
        if (!$user) return false;
        if ($user->isAdmin()) return true;
        if ($this->isCompleted() || $this->isCancelled()) return false;
        return (int) $this->vet_id === (int) $user->id;
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_DRAFT => 'Draft Pemeriksaan',
            self::STATUS_IN_PROGRESS => 'Sedang Diperiksa',
            self::STATUS_AWAITING_TESTS => 'Menunggu Hasil Lab/Tes',
            self::STATUS_COMPLETED => 'Selesai & Difinalisasi',
            self::STATUS_CANCELLED => 'Dibatalkan',
            default => ucfirst(str_replace('_', ' ', $this->status ?? 'Draft')),
        };
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_DRAFT => 'bg-slate-100 text-slate-700 border-slate-200',
            self::STATUS_IN_PROGRESS => 'bg-amber-100 text-amber-800 border-amber-200',
            self::STATUS_AWAITING_TESTS => 'bg-blue-100 text-blue-800 border-blue-200',
            self::STATUS_COMPLETED => 'bg-emerald-100 text-emerald-800 border-emerald-200',
            self::STATUS_CANCELLED => 'bg-rose-100 text-rose-800 border-rose-200',
            default => 'bg-slate-100 text-slate-800 border-slate-200',
        };
    }

    public function getServiceTypeLabelAttribute(): string
    {
        return $this->service_type === self::SERVICE_ONLINE
            ? 'Telekonsultasi Online'
            : 'Pemeriksaan Klinik';
    }

    public static function generateRecordNumber(): string
    {
        $prefix = 'MR-' . date('Ym') . '-';
        $latest = self::where('record_number', 'like', $prefix . '%')
            ->orderBy('id', 'desc')
            ->first();

        if ($latest && preg_match('/MR-\d{6}-(\d+)/', $latest->record_number, $matches)) {
            $seq = intval($matches[1]) + 1;
        } else {
            $seq = 1;
        }

        return $prefix . str_pad($seq, 4, '0', STR_PAD_LEFT);
    }
}
