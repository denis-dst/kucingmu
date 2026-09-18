<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ContactMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'email',
        'phone',
        'subject',
        'message',
        'status',
        'admin_response',
        'responded_by',
        'responded_at',
    ];

    protected $casts = [
        'responded_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function responder()
    {
        return $this->belongsTo(User::class, 'responded_by');
    }

    public function scopeUnread($query)
    {
        return $query->where('status', 'unread');
    }

    public function scopeResponded($query)
    {
        return $query->where('status', 'responded');
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'unread' => 'Belum Dibaca',
            'read' => 'Sudah Dibaca',
            'responded' => 'Sudah Dibalas',
            default => ucfirst($this->status),
        };
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'unread' => 'bg-rose-50 text-rose-800 border-rose-200',
            'read' => 'bg-amber-50 text-amber-800 border-amber-200',
            'responded' => 'bg-emerald-50 text-emerald-800 border-emerald-200',
            default => 'bg-slate-100 text-slate-700 border-slate-200',
        };
    }
}
