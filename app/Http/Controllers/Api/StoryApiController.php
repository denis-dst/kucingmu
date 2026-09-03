<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Cat;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class StoryApiController extends Controller
{
    /**
     * Get 24-hour Ephemeral Stories
     */
    public function index()
    {
        $users = User::whereHas('cats')
            ->orWhereIn('role', ['dokter', 'volunteer'])
            ->take(8)
            ->get();

        $groups = [];

        foreach ($users as $user) {
            $cat = $user->cats->first();
            $photo = $cat?->photo_url 
                ? (Str::startsWith($cat->photo_url, 'http') ? $cat->photo_url : url($cat->photo_url)) 
                : 'https://images.unsplash.com/photo-1514888286974-6c03e2ca1dba?w=600';

            $groups[] = [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'username' => Str::slug($user->name, '_'),
                    'email' => $user->email,
                    'role' => $user->role ?? 'member',
                    'is_verified' => $user->isDokter(),
                    'avatar_url' => $user->avatar_url ?? null,
                ],
                'has_unseen' => true,
                'stories' => [
                    [
                        'id' => 500 + $user->id,
                        'media_url' => $photo,
                        'media_type' => 'image',
                        'duration' => 5,
                        'caption' => 'Aktivitas bersama komunitas KucingMu hari ini 🐾',
                        'created_at' => now()->subHours(rand(1, 12))->toIso8601String(),
                        'expires_at' => now()->addHours(12)->toIso8601String(),
                    ]
                ],
            ];
        }

        return response()->json([
            'status' => 'success',
            'data' => $groups,
        ]);
    }
}
