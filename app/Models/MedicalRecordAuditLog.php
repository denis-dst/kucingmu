<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MedicalRecordAuditLog extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'medical_record_id',
        'actor_id',
        'action',
        'entity_type',
        'entity_id',
        'reason',
        'changes',
        'request_id',
        'occurred_at',
    ];

    protected $casts = [
        'changes' => 'array',
        'occurred_at' => 'datetime',
    ];

    public function medicalRecord()
    {
        return $this->belongsTo(MedicalRecord::class);
    }

    public function actor()
    {
        return $this->belongsTo(User::class, 'actor_id');
    }

    public static function log(
        MedicalRecord $record,
        User $actor,
        string $action,
        ?string $reason = null,
        ?array $changes = null,
        ?string $entityType = null,
        $entityId = null
    ): self {
        return self::create([
            'medical_record_id' => $record->id,
            'actor_id' => $actor->id,
            'action' => $action,
            'reason' => $reason,
            'changes' => $changes,
            'entity_type' => $entityType,
            'entity_id' => $entityId ? (string) $entityId : null,
            'request_id' => request()->header('X-Request-Id') ?: (string) \Illuminate\Support\Str::uuid(),
            'occurred_at' => now(),
        ]);
    }
}
