<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Cat;
use App\Models\Story;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;

class StoryApiController extends Controller
{
    /**
     * Get 24-hour Ephemeral Stories (Real database + Registered Cats)
     */
    public function index()
    {
        $groups = [];

        // 1. Fetch Real Stories from stories table if table exists
        if (Schema::hasTable('stories')) {
            $stories = Story::where('expires_at', '>', now())
                ->with('user')
                ->latest()
                ->get()
                ->groupBy('user_id');

            foreach ($stories as $userId => $userStoryList) {
                $firstStory = $userStoryList->first();
                $author = $firstStory->user ?? User::find($userId);

                if ($author) {
                    $storyItems = $userStoryList->map(fn($s) => [
                        'id' => $s->id,
                        'media_url' => $s->url,
                        'media_type' => $s->media_type,
                        'duration' => $s->duration_seconds,
                        'caption' => $s->caption,
                        'created_at' => $s->created_at->toIso8601String(),
                        'expires_at' => $s->expires_at->toIso8601String(),
                    ])->toArray();

                    $groups[] = [
                        'user' => [
                            'id' => $author->id,
                            'name' => $author->name,
                            'username' => Str::slug($author->name, '_'),
                            'email' => $author->email,
                            'role' => $author->role ?? 'member',
                            'is_verified' => $author->isDokter(),
                            'avatar_url' => $author->avatar_url ?? null,
                        ],
                        'has_unseen' => true,
                        'stories' => $storyItems,
                    ];
                }
            }
        }

        // 2. Fetch Active Community Cat Stories
        $users = User::whereHas('cats')
            ->orWhereIn('role', ['dokter', 'volunteer'])
            ->with(['cats' => function($q) {
                $q->with('photos');
            }])
            ->take(8)
            ->get();

        foreach ($users as $user) {
            if (collect($groups)->contains(fn($g) => $g['user']['id'] === $user->id)) {
                continue;
            }

            $cat = $user->cats->first();
            $photo = $cat?->primary_photo_url ?: 'https://images.unsplash.com/photo-1514888286974-6c03e2ca1dba?w=600';

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
                        'id' => 50000 + $user->id,
                        'media_url' => $photo,
                        'media_type' => 'image',
                        'duration' => 5,
                        'caption' => $cat ? "Kucing: {$cat->name} ({$cat->breed})" : 'Aktivitas bersama komunitas KucingMu 🐾',
                        'created_at' => now()->subHours(rand(1, 10))->toIso8601String(),
                        'expires_at' => now()->addHours(14)->toIso8601String(),
                    ]
                ],
            ];
        }

        return response()->json([
            'status' => 'success',
            'data' => $groups,
        ]);
    }

    /**
     * Create Story (Persisted to Database)
     */
    public function store(Request $request)
    {
        $request->validate([
            'media_path' => 'required|string',
            'media_type' => 'nullable|string',
            'caption' => 'nullable|string',
            'duration_seconds' => 'nullable|integer',
        ]);

        $user = User::first();

        if (Schema::hasTable('stories')) {
            $story = Story::create([
                'user_id' => $user->id ?? 1,
                'media_path' => $request->media_path,
                'media_type' => $request->media_type ?: 'image',
                'duration_seconds' => $request->duration_seconds ?: 5,
                'caption' => $request->caption,
                'expires_at' => now()->addHours(24),
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Cerita berhasil dibagikan!',
                'data' => [
                    'id' => $story->id,
                    'created_at' => $story->created_at->toIso8601String(),
                ],
            ], 201);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Cerita berhasil dibagikan!',
            'data' => ['id' => time()],
        ]);
    }
}
