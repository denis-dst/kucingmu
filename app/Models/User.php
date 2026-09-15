<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

use Illuminate\Support\Str;

#[Fillable(['name', 'email', 'password', 'phone', 'role', 'muhammadiyah_id', 'bio', 'avatar', 'api_token'])]
#[Hidden(['password', 'remember_token', 'api_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === 'superadmin';
    }

    public function isAdmin(): bool
    {
        return in_array($this->role, ['admin', 'superadmin']);
    }

    public function isDokter(): bool
    {
        return $this->role === 'dokter';
    }

    public function isVolunteer(): bool
    {
        return $this->role === 'volunteer';
    }

    public function isMember(): bool
    {
        return $this->role === 'member';
    }

    public function cats()
    {
        return $this->hasMany(Cat::class);
    }

    public function vetRecords()
    {
        return $this->hasMany(MedicalRecord::class, 'vet_id');
    }

    /**
     * Format any NBM input into 7-digit formatted string "x.xxx.xxx".
     * E.g. "1234567" -> "1.234.567", "1.234.567" -> "1.234.567".
     */
    public static function formatNbm(?string $nbm): ?string
    {
        if ($nbm === null || trim($nbm) === '') {
            return null;
        }

        // Extract only numeric digits
        $digits = preg_replace('/\D/', '', $nbm);

        if (empty($digits)) {
            return $nbm;
        }

        // If fewer than 7 digits, pad with leading zeros to 7 digits
        if (strlen($digits) < 7) {
            $digits = str_pad($digits, 7, '0', STR_PAD_LEFT);
        } elseif (strlen($digits) > 7) {
            $digits = substr($digits, 0, 7);
        }

        // Format as x.xxx.xxx (1 digit . 3 digits . 3 digits)
        return substr($digits, 0, 1) . '.' . substr($digits, 1, 3) . '.' . substr($digits, 4, 3);
    }

    /**
     * Accessor for formatted NBM.
     */
    public function getFormattedNbmAttribute(): ?string
    {
        return self::formatNbm($this->muhammadiyah_id);
    }

    /**
     * Accessor for full avatar URL.
     */
    public function getAvatarUrlAttribute(): ?string
    {
        if (empty($this->avatar)) {
            return null;
        }

        if (Str::startsWith($this->avatar, ['http://', 'https://'])) {
            return $this->avatar;
        }

        return asset('storage/' . ltrim($this->avatar, '/'));
    }

    /**
     * Mutator to ensure NBM is formatted as x.xxx.xxx upon saving.
     */
    public function setMuhammadiyahIdAttribute($value)
    {
        $this->attributes['muhammadiyah_id'] = self::formatNbm($value);
    }
}
