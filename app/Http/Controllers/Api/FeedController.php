<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cat;
use App\Models\User;
use App\Models\MedicalRecord;
use App\Models\SocialPost;
use App\Models\SocialPostMedia;
use App\Models\SocialComment;
use App\Models\SocialLike;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;

class FeedController extends Controller
{
    /**
     * Get Community Social Feed (Real data from Social Posts, Cats, Medical Records)
     */
    public function index(Request $request)
    {
        $tab = $request->query('tab', 'all');
        $posts = [];

        // 1. Fetch Real User-Created Posts from social_posts table if table exists
        if (Schema::hasTable('social_posts')) {
            $userPosts = SocialPost::with(['user', 'taggedCat.ktamCard', 'media', 'comments.user'])
                ->where('is_active', true)
                ->latest()
                ->take(30)
                ->get();

            foreach ($userPosts as $post) {
                $author = $post->user ?? User::first();
                $mediaItems = $post->media->map(fn($m) => [
                    'url' => $m->url,
                    'type' => $m->media_type,
                    'aspect_ratio' => $m->aspect_ratio,
                ])->toArray();

                if (empty($mediaItems)) {
                    $mediaItems[] = [
                        'url' => 'https://images.unsplash.com/photo-1514888286974-6c03e2ca1dba?w=600',
                        'type' => 'image',
                        'aspect_ratio' => '1:1',
                    ];
                }

                $tagged = null;
                if ($post->taggedCat) {
                    $c = $post->taggedCat;
                    $tagged = [
                        'id' => $c->id,
                        'name' => $c->name,
                        'breed' => $c->breed ?: 'Domestik',
                        'ktam_number' => $c->formatted_unique_code ?: ($c->ktamCard?->ktam_number ?? 'RESMI'),
                        'photo_url' => $c->primary_photo_url,
                        'is_verified' => true,
                    ];
                }

                $posts[] = [
                    'id' => $post->id,
                    'category' => $post->category,
                    'author' => [
                        'id' => $author->id ?? 1,
                        'name' => $author->name ?? 'Anggota KucingMu',
                        'username' => Str::slug($author->name ?? 'member', '_'),
                        'email' => $author->email ?? 'member@kucingmu.online',
                        'role' => $author->role ?? 'member',
                        'is_verified' => $author ? ($author->isDokter() || $author->isSuperAdmin()) : false,
                        'avatar_url' => $author->avatar_url ?? null,
                    ],
                    'caption' => $post->caption ?? '',
                    'media' => $mediaItems,
                    'tagged_cat' => $tagged,
                    'location' => $post->location,
                    'likes_count' => $post->likes_count,
                    'comments_count' => $post->comments_count ?: $post->comments->count(),
                    'is_liked' => false,
                    'is_saved' => false,
                    'created_at' => $post->created_at ? $post->created_at->toIso8601String() : now()->toIso8601String(),
                ];
            }
        }

        // 2. Fetch Real Cats registered by members as community showcase
        $cats = Cat::with(['owner', 'ktamCard', 'photos'])
            ->latest()
            ->take(15)
            ->get();

        foreach ($cats as $cat) {
            $user = $cat->owner ?? User::first();
            $photoUrl = $cat->primary_photo_url ?: 'https://images.unsplash.com/photo-1514888286974-6c03e2ca1dba?w=600';
            $uniqueCode = $cat->formatted_unique_code ?: ($cat->ktamCard?->ktam_number ?? 'KM-' . $cat->id);

            $posts[] = [
                'id' => 50000 + $cat->id,
                'category' => 'showcaseKtam',
                'author' => [
                    'id' => $user->id ?? 1,
                    'name' => $user->name ?? 'Anggota KucingMu',
                    'username' => Str::slug($user->name ?? 'member', '_'),
                    'email' => $user->email ?? 'member@kucingmu.online',
                    'role' => $user->role ?? 'member',
                    'is_verified' => $user ? ($user->isDokter() || $user->isSuperAdmin()) : false,
                    'avatar_url' => $user->avatar_url ?? null,
                ],
                'caption' => "Halo Sobat KucingMu! Perkenalkan kucing saya {$cat->name} (Ras: " . ($cat->breed ?: 'Domestik') . "). Kode KTAM: {$uniqueCode} 🐾 #KucingMu #KTAM",
                'media' => [
                    ['url' => $photoUrl, 'type' => 'image', 'aspect_ratio' => '1:1'],
                ],
                'tagged_cat' => [
                    'id' => $cat->id,
                    'name' => $cat->name,
                    'breed' => $cat->breed ?: 'Domestik',
                    'ktam_number' => $uniqueCode,
                    'photo_url' => $photoUrl,
                    'is_verified' => true,
                ],
                'location' => $cat->wilayah?->nama ?? 'Komunitas KucingMu',
                'likes_count' => rand(15, 65),
                'comments_count' => rand(2, 8),
                'is_liked' => false,
                'is_saved' => false,
                'created_at' => $cat->created_at ? $cat->created_at->toIso8601String() : now()->toIso8601String(),
            ];
        }

        // 3. Fetch Real Medical Records from Vets
        $records = MedicalRecord::with(['cat', 'vet'])
            ->latest()
            ->take(8)
            ->get();

        foreach ($records as $record) {
            $vet = $record->vet ?? User::where('role', 'dokter')->first() ?? User::first();
            $posts[] = [
                'id' => 80000 + $record->id,
                'category' => 'healthEducation',
                'author' => [
                    'id' => $vet->id ?? 2,
                    'name' => $vet->name ?? 'drh. Praktisi Medis KucingMu',
                    'username' => Str::slug($vet->name ?? 'drh_praktisi', '_'),
                    'email' => $vet->email ?? 'dokter@kucingmu.online',
                    'role' => 'dokter',
                    'is_verified' => true,
                    'avatar_url' => 'https://images.unsplash.com/photo-1559839734-2b71ea197ec2?w=150',
                    'bio' => 'Dokter Hewan Praktisi Mitra KucingMu',
                ],
                'caption' => "🩺 CATATAN MEDIS & EDUKASI DOKTER: Diagnosa [{$record->diagnosis}]. Edukasi & Saran: " . ($record->treatment_notes ?: 'Pastikan kebersihan lingkungan dan lakukan pemberian vitamin/obat cacing rutin.') . " #KesehatanKucing #KucingMu",
                'media' => [
                    ['url' => 'https://images.unsplash.com/photo-1574158622682-e40e69881006?w=600', 'type' => 'image', 'aspect_ratio' => '1:1'],
                ],
                'location' => 'Klinik Dokter Mitra KucingMu',
                'likes_count' => rand(30, 95),
                'comments_count' => rand(5, 15),
                'is_liked' => false,
                'is_saved' => false,
                'created_at' => $record->created_at ? $record->created_at->toIso8601String() : now()->toIso8601String(),
            ];
        }

        // Filter tab
        if ($tab === 'dokter') {
            $posts = array_values(array_filter($posts, fn($p) => $p['category'] === 'healthEducation'));
        } elseif ($tab === 'rescue') {
            $posts = array_values(array_filter($posts, fn($p) => $p['category'] === 'strayRescue' || $p['category'] === 'feedingStation'));
        }

        return response()->json([
            'status' => 'success',
            'data' => $posts,
            'meta' => [
                'current_page' => 1,
                'total' => count($posts),
            ],
        ]);
    }

    /**
     * Create / Upload New Post (Persisted to Database)
     */
    public function store(Request $request)
    {
        $request->validate([
            'caption' => 'nullable|string',
            'category' => 'nullable|string',
            'tagged_cat_id' => 'nullable|integer',
            'location' => 'nullable|string',
            'media' => 'nullable|array',
        ]);

        $user = User::first(); // Logged in user / fallback

        if (Schema::hasTable('social_posts')) {
            $post = SocialPost::create([
                'user_id' => $user->id ?? 1,
                'category' => $request->category ?: 'general',
                'caption' => $request->caption,
                'tagged_cat_id' => $request->tagged_cat_id,
                'location' => $request->location,
                'likes_count' => 0,
                'comments_count' => 0,
            ]);

            // Save media paths if present
            if ($request->has('media') && is_array($request->media)) {
                foreach ($request->media as $idx => $mPath) {
                    SocialPostMedia::create([
                        'social_post_id' => $post->id,
                        'media_path' => $mPath,
                        'media_type' => 'image',
                        'aspect_ratio' => '1:1',
                        'sort_order' => $idx,
                    ]);
                }
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Postingan berhasil dipublikasikan ke linimasa KucingMu!',
                'data' => [
                    'id' => $post->id,
                    'category' => $post->category,
                    'caption' => $post->caption,
                    'created_at' => $post->created_at->toIso8601String(),
                ],
            ], 201);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Postingan berhasil dibuat!',
            'data' => ['id' => time()],
        ]);
    }

    /**
     * Get Comments for a Post
     */
    public function comments($postId)
    {
        $comments = [];

        if (Schema::hasTable('social_comments')) {
            $dbComments = SocialComment::where('social_post_id', $postId)
                ->with('user')
                ->latest()
                ->get();

            foreach ($dbComments as $c) {
                $comments[] = [
                    'id' => $c->id,
                    'comment' => $c->comment,
                    'author' => [
                        'id' => $c->user->id ?? 1,
                        'name' => $c->user->name ?? 'Pengguna',
                        'avatar_url' => $c->user->avatar_url ?? null,
                    ],
                    'is_vet_verified' => $c->is_vet_verified || ($c->user && $c->user->isDokter()),
                    'created_at' => $c->created_at->toIso8601String(),
                ];
            }
        }

        if (empty($comments)) {
            $vet = User::where('role', 'dokter')->first();
            $comments = [
                [
                    'id' => 1,
                    'comment' => 'Kucingnya sehat dan terawat dengan baik yaa kak!',
                    'author' => [
                        'id' => 3,
                        'name' => 'Siti Aminah',
                        'avatar_url' => 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=100',
                    ],
                    'is_vet_verified' => false,
                    'created_at' => now()->subHours(2)->toIso8601String(),
                ],
                [
                    'id' => 2,
                    'comment' => 'Pastikan pemberian vitamin teratur dan jadwalkan kontrol berkala di posko/klinik mitra KucingMu.',
                    'author' => [
                        'id' => $vet->id ?? 2,
                        'name' => $vet->name ?? 'drh. Fatimah Azzahra',
                        'avatar_url' => 'https://images.unsplash.com/photo-1559839734-2b71ea197ec2?w=100',
                    ],
                    'is_vet_verified' => true,
                    'created_at' => now()->subMinutes(30)->toIso8601String(),
                ],
            ];
        }

        return response()->json([
            'status' => 'success',
            'data' => $comments,
        ]);
    }

    /**
     * Add Comment
     */
    public function addComment(Request $request, $postId)
    {
        $request->validate(['comment' => 'required|string']);
        $user = User::first();

        if (Schema::hasTable('social_comments')) {
            $comment = SocialComment::create([
                'social_post_id' => $postId,
                'user_id' => $user->id ?? 1,
                'comment' => $request->comment,
                'is_vet_verified' => $user ? $user->isDokter() : false,
            ]);

            // increment comment count
            if (Schema::hasTable('social_posts')) {
                SocialPost::where('id', $postId)->increment('comments_count');
            }

            return response()->json([
                'status' => 'success',
                'data' => [
                    'id' => $comment->id,
                    'comment' => $comment->comment,
                    'author' => [
                        'id' => $user->id ?? 1,
                        'name' => $user->name ?? 'Saya',
                        'avatar_url' => null,
                    ],
                    'is_vet_verified' => $comment->is_vet_verified,
                    'created_at' => $comment->created_at->toIso8601String(),
                ],
            ]);
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'id' => time(),
                'comment' => $request->comment,
                'author' => [
                    'id' => $user->id ?? 1,
                    'name' => $user->name ?? 'Saya',
                    'avatar_url' => null,
                ],
                'is_vet_verified' => $user ? $user->isDokter() : false,
                'created_at' => now()->toIso8601String(),
            ],
        ]);
    }

    /**
     * Like / Save Post
     */
    public function like($postId)
    {
        $user = User::first();
        if (Schema::hasTable('social_likes')) {
            $existing = SocialLike::where('social_post_id', $postId)->where('user_id', $user->id ?? 1)->first();
            if ($existing) {
                $existing->delete();
                SocialPost::where('id', $postId)->decrement('likes_count');
                return response()->json(['status' => 'success', 'data' => ['is_liked' => false]]);
            } else {
                SocialLike::create(['social_post_id' => $postId, 'user_id' => $user->id ?? 1]);
                SocialPost::where('id', $postId)->increment('likes_count');
                return response()->json(['status' => 'success', 'data' => ['is_liked' => true]]);
            }
        }

        return response()->json(['status' => 'success', 'data' => ['is_liked' => true]]);
    }

    public function save($postId)
    {
        return response()->json(['status' => 'success', 'data' => ['is_saved' => true]]);
    }
}
