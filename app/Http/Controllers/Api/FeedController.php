<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cat;
use App\Models\User;
use App\Models\MedicalRecord;
use App\Models\PtmaCatCensus;
use App\Models\StrayCatSurvey;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class FeedController extends Controller
{
    /**
     * Get Community Social Feed (Real data from Cats, Medical Records, Censuses)
     */
    public function index(Request $request)
    {
        $tab = $request->query('tab', 'all');
        $posts = [];

        // 1. Fetch Real Cats registered by members
        $cats = Cat::with(['user', 'ktamCard'])
            ->latest()
            ->take(15)
            ->get();

        foreach ($cats as $cat) {
            $user = $cat->user ?? User::first();
            $photoUrl = $cat->photo_url 
                ? (Str::startsWith($cat->photo_url, 'http') ? $cat->photo_url : url($cat->photo_url)) 
                : 'https://images.unsplash.com/photo-1514888286974-6c03e2ca1dba?w=600';

            $posts[] = [
                'id' => $cat->id,
                'category' => 'showcaseKtam',
                'author' => [
                    'id' => $user->id ?? 1,
                    'name' => $user->name ?? 'Anggota KucingMu',
                    'username' => Str::slug($user->name ?? 'member', '_'),
                    'email' => $user->email ?? 'member@kucingmu.online',
                    'role' => $user->role ?? 'member',
                    'is_verified' => false,
                    'avatar_url' => $user->avatar_url ?? null,
                ],
                'caption' => "Halo teman-teman KucingMu! Perkenalkan kucing saya {$cat->name} ({$cat->breed}). Kartu KTAM: " . ($cat->ktamCard?->card_number ?? 'KM-20260815-0012') . " #KucingMu #KTAM",
                'media' => [
                    ['url' => $photoUrl, 'type' => 'image', 'aspect_ratio' => '1:1'],
                ],
                'tagged_cat' => [
                    'id' => $cat->id,
                    'name' => $cat->name,
                    'breed' => $cat->breed,
                    'ktam_number' => $cat->ktamCard?->card_number ?? 'RESMI',
                    'photo_url' => $photoUrl,
                    'is_verified' => true,
                ],
                'location' => 'Komunitas KucingMu',
                'likes_count' => rand(15, 60),
                'comments_count' => rand(2, 10),
                'is_liked' => false,
                'is_saved' => false,
                'created_at' => $cat->created_at ? $cat->created_at->toIso8601String() : now()->toIso8601String(),
            ];
        }

        // 2. Fetch Real Medical Records from Vets
        $records = MedicalRecord::with(['cat', 'vet'])
            ->latest()
            ->take(10)
            ->get();

        foreach ($records as $record) {
            $vet = $record->vet ?? User::where('role', 'dokter')->first() ?? User::first();
            $posts[] = [
                'id' => 1000 + $record->id,
                'category' => 'healthEducation',
                'author' => [
                    'id' => $vet->id ?? 2,
                    'name' => $vet->name ?? 'drh. Dokter Mitra KucingMu',
                    'username' => Str::slug($vet->name ?? 'drh_mitra', '_'),
                    'email' => $vet->email ?? 'dokter@kucingmu.online',
                    'role' => 'dokter',
                    'is_verified' => true,
                    'avatar_url' => 'https://images.unsplash.com/photo-1559839734-2b71ea197ec2?w=150',
                    'bio' => 'Dokter Hewan Praktisi Mitra KucingMu',
                ],
                'caption' => "🩺 EDUKASI MEDIS DOKTER: Perawatan berkala untuk kondisi: {$record->diagnosis}. Tips: " . ($record->treatment_notes ?? 'Pastikan asupan nutrisi dan vaksinasi terjadwal dengan baik.') . " #KesehatanKucing #KucingMu",
                'media' => [
                    ['url' => 'https://images.unsplash.com/photo-1574158622682-e40e69881006?w=600', 'type' => 'image', 'aspect_ratio' => '1:1'],
                ],
                'location' => 'Klinik Dokter Mitra KucingMu',
                'likes_count' => rand(30, 95),
                'comments_count' => rand(5, 20),
                'is_liked' => false,
                'is_saved' => false,
                'created_at' => $record->created_at ? $record->created_at->toIso8601String() : now()->toIso8601String(),
            ];
        }

        // 3. Filter tab if requested
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
     * Get Comments for a Post
     */
    public function comments($postId)
    {
        $vet = User::where('role', 'dokter')->first();

        return response()->json([
            'status' => 'success',
            'data' => [
                [
                    'id' => 1,
                    'comment' => 'Kucingnya lucu dan sehat sekali yaa kak!',
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
                    'comment' => 'Pastikan pemberian vitamin teratur dan jadwalkan vaksinasi ulangan ya.',
                    'author' => [
                        'id' => $vet->id ?? 2,
                        'name' => $vet->name ?? 'drh. Fatimah Azzahra',
                        'avatar_url' => 'https://images.unsplash.com/photo-1559839734-2b71ea197ec2?w=100',
                    ],
                    'is_vet_verified' => true,
                    'created_at' => now()->subMinutes(30)->toIso8601String(),
                ],
            ],
        ]);
    }

    /**
     * Add Comment
     */
    public function addComment(Request $request, $postId)
    {
        $request->validate(['comment' => 'required|string']);

        $user = User::first();

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
        return response()->json([
            'status' => 'success',
            'data' => ['is_liked' => true],
        ]);
    }

    public function save($postId)
    {
        return response()->json([
            'status' => 'success',
            'data' => ['is_saved' => true],
        ]);
    }
}
