<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class AdminUserController extends Controller
{
    /**
     * Display a paginated listing of users with search and role filters.
     */
    public function index(Request $request)
    {
        $roleFilter = $request->get('role', 'all');
        $search = trim($request->get('search', ''));
        $sort = $request->get('sort', 'created_at');
        $direction = strtolower($request->get('direction', 'desc')) === 'asc' ? 'asc' : 'desc';

        $query = User::withCount(['cats', 'vetRecords']);

        if ($roleFilter !== 'all' && in_array($roleFilter, ['admin', 'superadmin', 'dokter', 'volunteer', 'member'])) {
            $query->where('role', $roleFilter);
        }

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('muhammadiyah_id', 'like', "%{$search}%");
            });
        }

        switch ($sort) {
            case 'name':
                $query->orderBy('name', $direction);
                break;
            case 'role':
                $query->orderBy('role', $direction);
                break;
            case 'cats_count':
                $query->orderBy('cats_count', $direction);
                break;
            case 'created_at':
            default:
                $query->orderBy('created_at', $direction);
                break;
        }

        $users = $query->paginate(15)->withQueryString();

        // Statistics for widgets
        $stats = [
            'total' => User::count(),
            'member' => User::where('role', 'member')->count(),
            'dokter' => User::where('role', 'dokter')->count(),
            'volunteer' => User::where('role', 'volunteer')->count(),
            'admin' => User::whereIn('role', ['admin', 'superadmin'])->count(),
        ];

        return view('admin.users.index', compact('users', 'stats', 'roleFilter', 'search', 'sort', 'direction'));
    }

    /**
     * Update user role (e.g. hire active member to volunteer, dokter, or admin).
     */
    public function updateRole(Request $request, User $user)
    {
        $validated = $request->validate([
            'role' => ['required', Rule::in(['member', 'volunteer', 'dokter', 'admin', 'superadmin'])],
        ]);

        $currentUser = Auth::user();

        // Prevent admin from changing their own role to prevent lockout
        if ($user->id === $currentUser->id) {
            return back()->with('error', 'Anda tidak dapat mengubah peran akun Anda sendiri.');
        }

        // Only superadmin can assign superadmin role
        if ($validated['role'] === 'superadmin' && !$currentUser->isSuperAdmin()) {
            return back()->with('error', 'Hanya Super Administrator yang dapat menetapkan peran Superadmin.');
        }

        $oldRole = $user->role;
        $newRole = $validated['role'];

        $user->update(['role' => $newRole]);

        $roleLabels = [
            'member' => 'Member / Pemilik Kucing',
            'volunteer' => 'Relawan Sensus PTMA & Surveilans',
            'dokter' => 'Dokter Hewan (Vet)',
            'admin' => 'Administrator',
            'superadmin' => 'Super Administrator',
        ];

        $roleName = $roleLabels[$newRole] ?? ucfirst($newRole);

        return back()->with('success', "Peran untuk {$user->name} berhasil diperbarui menjadi {$roleName}.");
    }

    /**
     * Impersonate a target user.
     */
    public function impersonate(User $user)
    {
        $currentUser = Auth::user();

        // Check if caller has admin permissions (or if already impersonating, check session)
        if (!$currentUser->isAdmin() && !session()->has('impersonator_id')) {
            abort(403, 'Akses tidak diizinkan. Hanya administrator yang dapat melakukan impersonasi.');
        }

        // Cannot impersonate yourself
        if ($user->id === $currentUser->id) {
            return back()->with('error', 'Anda sudah masuk dengan akun ini.');
        }

        // If not already in impersonation mode, record the original admin's ID
        if (!session()->has('impersonator_id')) {
            session(['impersonator_id' => $currentUser->id]);
        }

        // Log in as target user
        Auth::login($user);

        return redirect()->route('dashboard')->with('success', "Mode Impersonasi Aktif: Anda sekarang masuk sebagai {$user->name} ({$user->role}).");
    }

    /**
     * Leave impersonation and restore original admin session.
     */
    public function leaveImpersonation(Request $request)
    {
        if (!session()->has('impersonator_id')) {
            return redirect()->route('dashboard')->with('error', 'Tidak ada sesi impersonasi aktif.');
        }

        $originalAdminId = session('impersonator_id');
        $originalAdmin = User::find($originalAdminId);

        if (!$originalAdmin) {
            session()->forget('impersonator_id');
            return redirect()->route('dashboard')->with('error', 'Akun administrator asal tidak ditemukan.');
        }

        // Clear impersonator session flag
        session()->forget('impersonator_id');

        // Restore original admin login
        Auth::login($originalAdmin);

        return redirect()->route('admin.users.index')->with('success', "Selesai: Anda telah kembali ke akun Administrator ({$originalAdmin->name}).");
    }
}
