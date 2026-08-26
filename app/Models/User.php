<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'phone', 'role', 'muhammadiyah_id'])]
#[Hidden(['password', 'remember_token'])]
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
}
