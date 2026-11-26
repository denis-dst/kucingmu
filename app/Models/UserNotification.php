<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserNotification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'sender_id',
        'type',
        'title',
        'message',
        'url',
        'icon',
        'data',
        'read_at',
    ];

    protected $casts = [
        'data' => 'array',
        'read_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }

    public function isRead(): bool
    {
        return $this->read_at !== null;
    }

    public function markAsRead(): bool
    {
        if ($this->read_at === null) {
            return $this->update(['read_at' => now()]);
        }
        return true;
    }

    /**
     * Dispatch a user notification safely.
     */
    public static function make(
        int $userId,
        string $title,
        string $message,
        ?string $url = null,
        string $type = 'general',
        ?int $senderId = null,
        string $icon = 'bell',
        ?array $data = null
    ): self {
        return self::create([
            'user_id' => $userId,
            'sender_id' => $senderId,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'url' => $url,
            'icon' => $icon,
            'data' => $data,
        ]);
    }

    /**
     * Synchronize deceased cat notifications for a specific user.
     * Ensures any member (including members without cats) receives notifications for deceased cats.
     */
    public static function syncDeceasedCatNotificationsForUser(?User $user, bool $force = false): void
    {
        if (!$user) {
            return;
        }

        $latestDeceasedUpdate = Cat::whereIn('status', ['mati', 'deceased'])->max('updated_at');
        if (!$latestDeceasedUpdate) {
            return;
        }

        $sessionKey = 'deceased_cats_synced_' . $user->id;
        if (!$force && session($sessionKey) && session($sessionKey) >= $latestDeceasedUpdate) {
            return;
        }

        // Get IDs of deceased cats that the user already has a notification for
        $existingNotifs = self::where('user_id', $user->id)
            ->whereIn('type', ['cat_deceased', 'cat_memorial'])
            ->whereNotNull('data')
            ->get();

        $existingNotifCatIds = [];
        foreach ($existingNotifs as $notif) {
            $catId = is_array($notif->data) ? ($notif->data['cat_id'] ?? null) : null;
            if ($catId) {
                $existingNotifCatIds[] = (int) $catId;
            }
        }
        $existingNotifCatIds = array_unique($existingNotifCatIds);

        $query = Cat::whereIn('status', ['mati', 'deceased'])->with('owner');
        if (!empty($existingNotifCatIds)) {
            $query->whereNotIn('id', $existingNotifCatIds);
        }

        $unnotifiedCats = $query->get();
        if ($unnotifiedCats->isEmpty()) {
            session([$sessionKey => now()]);
            return;
        }

        $now = now();
        $batch = [];

        foreach ($unnotifiedCats as $cat) {
            $isOwner = (int) $cat->user_id === (int) $user->id;
            $ownerName = $cat->owner->name ?? 'Member KucingMu';

            try {
                $memorialUrl = route('cat.memorial', $cat->id);
            } catch (\Throwable $e) {
                $memorialUrl = url('/cat/' . $cat->id . '/memorial');
            }

            if ($isOwner) {
                $batch[] = [
                    'user_id' => $user->id,
                    'sender_id' => null,
                    'type' => 'cat_memorial',
                    'title' => '🕊️ Belasungkawa & Kenangan: ' . $cat->name . ' Telah Berpulang',
                    'message' => "Kami segenap keluarga besar KucingMu turut berduka cita sedalam-dalamnya atas kepergian anabul tercinta Anda, {$cat->name}. Halaman memorial kenangan dan poster belasungkawa kini telah aktif untuk Anda bagikan.",
                    'url' => $memorialUrl,
                    'icon' => 'heart',
                    'data' => json_encode([
                        'cat_id' => $cat->id,
                        'cat_name' => $cat->name,
                        'event' => 'cat_deceased',
                        'action' => 'memorial',
                    ]),
                    'read_at' => null,
                    'created_at' => $cat->updated_at ?? $now,
                    'updated_at' => $cat->updated_at ?? $now,
                ];
            } else {
                $batch[] = [
                    'user_id' => $user->id,
                    'sender_id' => $cat->user_id,
                    'type' => 'cat_deceased',
                    'title' => '🕊️ Kabar Duka Anabul: ' . $cat->name,
                    'message' => "Anabul kesayangan {$ownerName}, {$cat->name}, telah berpulang ke Rahmatullah. Mari sampaikan doa, simpati hangat, dan ucapan belasungkawa Anda.",
                    'url' => $memorialUrl,
                    'icon' => 'heart',
                    'data' => json_encode([
                        'cat_id' => $cat->id,
                        'cat_name' => $cat->name,
                        'owner_name' => $ownerName,
                        'event' => 'cat_deceased_community',
                    ]),
                    'read_at' => null,
                    'created_at' => $cat->updated_at ?? $now,
                    'updated_at' => $cat->updated_at ?? $now,
                ];
            }
        }

        if (!empty($batch)) {
            self::insert($batch);
        }

        session([$sessionKey => now()]);
    }
}
