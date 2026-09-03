<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cat;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ExploreApiController extends Controller
{
    /**
     * Explore 3x3 Grid
     */
    public function explore()
    {
        $cats = Cat::with(['owner', 'photos'])->latest()->take(21)->get();
        $items = [];

        foreach ($cats as $cat) {
            $photo = $cat->primary_photo_url ?: 'https://images.unsplash.com/photo-1514888286974-6c03e2ca1dba?w=500';

            $items[] = [
                'id' => $cat->id,
                'media' => [
                    ['url' => $photo, 'type' => 'image'],
                ],
                'caption' => "Kucing {$cat->name} (Ras: " . ($cat->breed ?: 'Domestik') . ") #KucingMu",
                'author' => [
                    'id' => $cat->user_id,
                    'name' => $cat->owner?->name ?? 'Anggota KucingMu',
                    'username' => Str::slug($cat->owner?->name ?? 'member', '_'),
                    'role' => $cat->owner?->role ?? 'member',
                ],
            ];
        }

        return response()->json([
            'status' => 'success',
            'data' => $items,
        ]);
    }

    /**
     * Search Posts, Users, Cats
     */
    public function search(Request $request)
    {
        $q = $request->query('q', '');
        $cats = Cat::where('name', 'like', "%{$q}%")
            ->orWhere('breed', 'like', "%{$q}%")
            ->with(['owner', 'photos'])
            ->take(15)
            ->get();

        $items = [];
        foreach ($cats as $cat) {
            $photo = $cat->primary_photo_url ?: 'https://images.unsplash.com/photo-1514888286974-6c03e2ca1dba?w=500';

            $items[] = [
                'id' => $cat->id,
                'media' => [
                    ['url' => $photo, 'type' => 'image'],
                ],
                'caption' => "Kucing {$cat->name} (Ras: " . ($cat->breed ?: 'Domestik') . ") #KucingMu",
                'author' => [
                    'id' => $cat->user_id,
                    'name' => $cat->owner?->name ?? 'Anggota KucingMu',
                    'username' => Str::slug($cat->owner?->name ?? 'member', '_'),
                    'role' => $cat->owner?->role ?? 'member',
                ],
            ];
        }

        return response()->json([
            'status' => 'success',
            'data' => $items,
        ]);
    }
}
