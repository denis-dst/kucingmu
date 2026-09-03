<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Cat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * User Login API
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string',
            'password' => 'required|string',
        ]);

        $loginInput = $request->email;
        $user = User::where('email', $loginInput)
            ->orWhere('phone', $loginInput)
            ->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Email/Nomor HP atau kata sandi yang Anda masukkan salah.',
            ], 422);
        }

        // Generate token
        $token = 'km_' . Str::random(64);

        // Load registered cats with KTAM
        $cats = Cat::where('user_id', $user->id)
            ->with('ktamCard')
            ->get()
            ->map(function ($cat) {
                return [
                    'id' => $cat->id,
                    'name' => $cat->name,
                    'breed' => $cat->breed,
                    'ktam_number' => $cat->ktamCard?->card_number ?? $cat->ktam_number,
                    'photo_url' => $cat->photo_url ? url($cat->photo_url) : null,
                    'is_verified' => $cat->is_verified ?? true,
                ];
            });

        $userData = [
            'id' => $user->id,
            'name' => $user->name,
            'username' => Str::slug($user->name, '_') ?: 'user_' . $user->id,
            'email' => $user->email,
            'phone' => $user->phone,
            'role' => $user->role ?? 'member',
            'is_verified' => $user->isDokter() || $user->isSuperAdmin(),
            'avatar_url' => $user->avatar_url ?? null,
            'bio' => $user->bio ?? null,
            'muhammadiyah_id' => $user->formatted_nbm ?? $user->muhammadiyah_id,
            'posts_count' => Cat::where('user_id', $user->id)->count(),
            'followers_count' => 0,
            'following_count' => 0,
            'registered_cats' => $cats,
        ];

        return response()->json([
            'status' => 'success',
            'message' => 'Login berhasil!',
            'data' => [
                'token' => $token,
                'user' => $userData,
            ],
        ]);
    }

    /**
     * User Register API
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:6',
            'phone' => 'nullable|string|max:20',
            'role' => 'nullable|string|in:member,dokter,volunteer,relawan',
            'muhammadiyah_id' => 'nullable|string|max:50',
        ]);

        $role = $request->role === 'relawan' ? 'volunteer' : ($request->role ?? 'member');

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'role' => $role,
            'muhammadiyah_id' => $request->muhammadiyah_id,
        ]);

        $token = 'km_' . Str::random(64);

        return response()->json([
            'status' => 'success',
            'message' => 'Pendaftaran akun berhasil!',
            'data' => [
                'token' => $token,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'username' => Str::slug($user->name, '_'),
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'role' => $user->role,
                    'is_verified' => false,
                    'avatar_url' => null,
                    'bio' => null,
                    'muhammadiyah_id' => $user->formatted_nbm,
                    'posts_count' => 0,
                    'followers_count' => 0,
                    'following_count' => 0,
                    'registered_cats' => [],
                ],
            ],
        ], 201);
    }

    /**
     * Get Current Profile (Me)
     */
    public function me(Request $request)
    {
        $user = User::first(); // Fallback active or token user
        if (!$user) {
            return response()->json(['status' => 'error', 'message' => 'User not found'], 404);
        }

        $cats = Cat::where('user_id', $user->id)
            ->with('ktamCard')
            ->get()
            ->map(function ($cat) {
                return [
                    'id' => $cat->id,
                    'name' => $cat->name,
                    'breed' => $cat->breed,
                    'ktam_number' => $cat->ktamCard?->card_number ?? $cat->ktam_number,
                    'photo_url' => $cat->photo_url ? url($cat->photo_url) : null,
                    'is_verified' => true,
                ];
            });

        return response()->json([
            'status' => 'success',
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'username' => Str::slug($user->name, '_'),
                'email' => $user->email,
                'phone' => $user->phone,
                'role' => $user->role ?? 'member',
                'is_verified' => $user->isDokter() || $user->isSuperAdmin(),
                'avatar_url' => $user->avatar_url ?? null,
                'bio' => $user->bio ?? null,
                'muhammadiyah_id' => $user->formatted_nbm,
                'posts_count' => Cat::where('user_id', $user->id)->count(),
                'followers_count' => 0,
                'following_count' => 0,
                'registered_cats' => $cats,
            ],
        ]);
    }

    /**
     * Logout
     */
    public function logout(Request $request)
    {
        return response()->json([
            'status' => 'success',
            'message' => 'Berhasil keluar akun.',
        ]);
    }
}
