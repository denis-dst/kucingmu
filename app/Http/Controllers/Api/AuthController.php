<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Cat;
use App\Services\ImageCompressionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    /**
     * Helper to resolve authenticated user from Bearer token, cache, or request
     */
    public static function getAuthUser(Request $request): ?User
    {
        $token = $request->bearerToken();
        if ($token) {
            $cachedId = Cache::get('api_token_' . $token);
            if ($cachedId) {
                $user = User::find($cachedId);
                if ($user) {
                    return $user;
                }
            }

            $user = User::where('api_token', $token)->first();
            if ($user) {
                Cache::put('api_token_' . $token, $user->id, now()->addDays(90));
                return $user;
            }
        }

        // Check if user_id explicitly provided
        if ($request->filled('user_id')) {
            $user = User::find($request->user_id);
            if ($user) {
                return $user;
            }
        }

        // Fallback to active member if available
        $defaultUser = User::where('email', 'member@kucingmu.online')
            ->orWhere('email', 'member@kucingmu.com')
            ->first();

        return $defaultUser ?: User::first();
    }

    /**
     * Format user payload for API response
     */
    public static function formatUserData(User $user): array
    {
        $cats = Cat::where('user_id', $user->id)
            ->with(['ktamCard', 'photos'])
            ->get()
            ->map(function ($cat) {
                return [
                    'id' => $cat->id,
                    'name' => $cat->name,
                    'breed' => $cat->breed ?: 'Domestik',
                    'gender' => $cat->gender,
                    'color' => $cat->color,
                    'date_of_birth' => $cat->date_of_birth ? $cat->date_of_birth->format('d-m-Y') : null,
                    'ktam_number' => $cat->formatted_unique_code ?: ($cat->ktamCard?->ktam_number ?? 'KM-' . $cat->id),
                    'photo_url' => $cat->primary_photo_url,
                    'is_verified' => true,
                ];
            });

        return [
            'id' => $user->id,
            'name' => $user->name,
            'username' => Str::slug($user->name, '_') ?: 'user_' . $user->id,
            'email' => $user->email,
            'phone' => $user->phone,
            'role' => $user->role ?? 'member',
            'is_verified' => $user->isDokter() || $user->isSuperAdmin(),
            'avatar_url' => $user->avatar_url,
            'bio' => $user->bio,
            'muhammadiyah_id' => $user->formatted_nbm ?? $user->muhammadiyah_id,
            'posts_count' => Cat::where('user_id', $user->id)->count(),
            'followers_count' => 0,
            'following_count' => 0,
            'registered_cats' => $cats,
        ];
    }

    /**
     * User Login API
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string',
            'password' => 'required|string',
        ]);

        $loginInput = trim($request->email);
        $user = User::where('email', $loginInput)
            ->orWhere('phone', $loginInput)
            ->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Email/Nomor HP atau kata sandi yang Anda masukkan salah.',
            ], 422);
        }

        // Generate token & persist
        $token = 'km_' . Str::random(64);
        $user->api_token = $token;
        $user->save();
        Cache::put('api_token_' . $token, $user->id, now()->addDays(90));

        return response()->json([
            'status' => 'success',
            'message' => 'Login berhasil!',
            'data' => [
                'token' => $token,
                'user' => self::formatUserData($user),
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
        $token = 'km_' . Str::random(64);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'role' => $role,
            'muhammadiyah_id' => $request->muhammadiyah_id,
            'api_token' => $token,
        ]);

        Cache::put('api_token_' . $token, $user->id, now()->addDays(90));

        return response()->json([
            'status' => 'success',
            'message' => 'Pendaftaran akun berhasil!',
            'data' => [
                'token' => $token,
                'user' => self::formatUserData($user),
            ],
        ], 201);
    }

    /**
     * Get Current Profile (Me)
     */
    public function me(Request $request)
    {
        $user = self::getAuthUser($request);
        if (!$user) {
            return response()->json(['status' => 'error', 'message' => 'Pengguna tidak ditemukan'], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => self::formatUserData($user),
        ]);
    }

    /**
     * Update User Profile API
     */
    public function updateProfile(Request $request)
    {
        $user = self::getAuthUser($request);
        if (!$user) {
            return response()->json(['status' => 'error', 'message' => 'Sesi tidak valid'], 401);
        }

        $request->validate([
            'name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'bio' => 'nullable|string|max:1000',
            'muhammadiyah_id' => 'nullable|string|max:50',
            'password' => 'nullable|string|min:6',
        ]);

        if ($request->filled('name')) {
            $user->name = $request->name;
        }

        if ($request->has('phone')) {
            $user->phone = $request->phone;
        }

        if ($request->has('bio')) {
            $user->bio = $request->bio;
        }

        if ($request->has('muhammadiyah_id')) {
            $user->muhammadiyah_id = $request->muhammadiyah_id;
        }

        // Handle Avatar upload (multipart file, base64, or string)
        if ($request->hasFile('avatar')) {
            $avatarPath = ImageCompressionService::compressAndStore($request->file('avatar'), 'avatars', 'public', 150);
            if ($avatarPath) {
                $user->avatar = $avatarPath;
            }
        } elseif ($request->filled('avatar_base64')) {
            $avatarPath = ImageCompressionService::compressAndStore($request->avatar_base64, 'avatars', 'public', 150);
            if ($avatarPath) {
                $user->avatar = $avatarPath;
            }
        }

        // Handle optional password update
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Profil berhasil diperbarui!',
            'data' => self::formatUserData($user),
        ]);
    }

    /**
     * Logout
     */
    public function logout(Request $request)
    {
        $token = $request->bearerToken();
        if ($token) {
            Cache::forget('api_token_' . $token);
            User::where('api_token', $token)->update(['api_token' => null]);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Berhasil keluar akun.',
        ]);
    }
}
