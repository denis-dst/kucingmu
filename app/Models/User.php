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

#[Fillable(['name', 'email', 'password', 'phone', 'role', 'roles', 'muhammadiyah_id', 'bio', 'avatar', 'api_token'])]
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
            'roles' => 'array',
        ];
    }

    /**
     * Get all assigned roles for this user.
     * Every user in the system is always guaranteed to have the 'member' role.
     *
     * @return array<string>
     */
    public function getAllRoles(): array
    {
        $rawRoles = $this->roles;
        if (is_string($rawRoles)) {
            $decoded = json_decode($rawRoles, true);
            $rawRoles = is_array($decoded) ? $decoded : [$rawRoles];
        } elseif (!is_array($rawRoles)) {
            $rawRoles = [];
        }

        $all = array_merge($rawRoles, array_filter([$this->role]));
        $all[] = 'member'; // Everyone is always a member / cat owner

        if (in_array('superadmin', $all)) {
            $all[] = 'admin';
        }

        return array_values(array_unique(array_map('strtolower', array_map('trim', $all))));
    }

    /**
     * Check if user has a specific role or any of the given roles.
     *
     * @param string|array ...$roles
     * @return bool
     */
    public function hasRole(...$roles): bool
    {
        $userRoles = $this->getAllRoles();
        
        foreach ($roles as $role) {
            if (is_array($role)) {
                foreach ($role as $r) {
                    if (in_array(strtolower(trim($r)), $userRoles)) {
                        return true;
                    }
                }
            } else {
                if (in_array(strtolower(trim($role)), $userRoles)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Get the currently active workspace role from session.
     * Defaults to the highest staff role or 'member'.
     *
     * @return string
     */
    public function getActiveRole(): string
    {
        $available = $this->getAllRoles();
        $sessionRole = session('active_role');

        if ($sessionRole && in_array(strtolower(trim($sessionRole)), $available)) {
            return strtolower(trim($sessionRole));
        }

        $priorities = ['superadmin', 'admin', 'dokter', 'volunteer', 'member'];
        foreach ($priorities as $p) {
            if (in_array($p, $available)) {
                return $p;
            }
        }

        return 'member';
    }

    /**
     * Set the currently active workspace role in session.
     *
     * @param string $role
     * @return bool
     */
    public function setActiveRole(string $role): bool
    {
        $role = strtolower(trim($role));
        if (in_array($role, $this->getAllRoles())) {
            session(['active_role' => $role]);
            return true;
        }

        return false;
    }

    /**
     * Get human-friendly metadata for a workspace role.
     *
     * @param string|null $role
     * @return array{name: string, short_name: string, icon: string, badge_class: string, desc: string}
     */
    public static function getWorkspaceMeta(?string $role = null): array
    {
        return match ($role) {
            'superadmin' => [
                'name' => 'Super Administrator',
                'short_name' => 'Superadmin',
                'icon' => '⚡',
                'badge_class' => 'bg-purple-100 text-purple-900 border-purple-200',
                'desc' => 'Akses penuh sistem, konfigurasi, dan wewenang superadmin.',
            ],
            'admin' => [
                'name' => 'Ruang Administrator',
                'short_name' => 'Admin',
                'icon' => '🛡️',
                'badge_class' => 'bg-amber-100 text-amber-900 border-amber-200',
                'desc' => 'Manajemen event, pengguna, master wilayah, dan verifikasi KTAM.',
            ],
            'dokter' => [
                'name' => 'Ruang Dokter Hewan',
                'short_name' => 'Dokter',
                'icon' => '🩺',
                'badge_class' => 'bg-emerald-100 text-emerald-900 border-emerald-200',
                'desc' => 'Pemeriksaan antrian kucing pasien, diagnosis, dan rekam medis.',
            ],
            'volunteer' => [
                'name' => 'Ruang Relawan Lapangan',
                'short_name' => 'Relawan',
                'icon' => '📋',
                'badge_class' => 'bg-indigo-100 text-indigo-900 border-indigo-200',
                'desc' => 'Sensus stray cat PTMA, surveilans kampus, dan registrasi di lokasi.',
            ],
            default => [
                'name' => 'Ruang Kucing Saya (Member)',
                'short_name' => 'Member',
                'icon' => '🐱',
                'badge_class' => 'bg-teal-100 text-teal-900 border-teal-200',
                'desc' => 'Daftar kucing peliharaan, rekam kesehatan, unduh KTAM, dan buat janji.',
            ],
        };
    }

    public function isSuperAdmin(): bool
    {
        return $this->hasRole('superadmin');
    }

    public function isAdmin(): bool
    {
        return $this->hasRole('admin', 'superadmin');
    }

    public function isDokter(): bool
    {
        return $this->hasRole('dokter');
    }

    public function isVolunteer(): bool
    {
        return $this->hasRole('volunteer');
    }

    public function isMember(): bool
    {
        return $this->hasRole('member');
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
