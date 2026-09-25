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
            $query->where(function ($q) use ($roleFilter) {
                $q->where('role', $roleFilter)
                  ->orWhereJsonContains('roles', $roleFilter);
            });
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
            case 'email':
                $query->orderBy('email', $direction);
                break;
            case 'phone':
                $query->orderBy('phone', $direction);
                break;
            case 'muhammadiyah_id':
                $query->orderBy('muhammadiyah_id', $direction);
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

        // Statistics for widgets in a single aggregated query
        $userStats = User::selectRaw("
            COUNT(*) as total,
            COUNT(CASE WHEN role = 'member' OR JSON_CONTAINS(COALESCE(roles, '[]'), '\"member\"') THEN 1 END) as member,
            COUNT(CASE WHEN role = 'dokter' OR JSON_CONTAINS(COALESCE(roles, '[]'), '\"dokter\"') THEN 1 END) as dokter,
            COUNT(CASE WHEN role = 'volunteer' OR JSON_CONTAINS(COALESCE(roles, '[]'), '\"volunteer\"') THEN 1 END) as volunteer,
            COUNT(CASE WHEN role IN ('admin', 'superadmin') OR JSON_CONTAINS(COALESCE(roles, '[]'), '\"admin\"') OR JSON_CONTAINS(COALESCE(roles, '[]'), '\"superadmin\"') THEN 1 END) as admin
        ")->first();

        $stats = [
            'total' => (int) ($userStats->total ?? 0),
            'member' => (int) ($userStats->member ?? 0),
            'dokter' => (int) ($userStats->dokter ?? 0),
            'volunteer' => (int) ($userStats->volunteer ?? 0),
            'admin' => (int) ($userStats->admin ?? 0),
        ];

        return view('admin.users.index', compact('users', 'stats', 'roleFilter', 'search', 'sort', 'direction'));
    }

    /**
     * Update user role (e.g. hire active member to volunteer, dokter, or admin).
     * Supports multiple roles with workspace switching.
     */
    public function updateRole(Request $request, User $user)
    {
        $validated = $request->validate([
            'roles' => ['nullable', 'array'],
            'roles.*' => [Rule::in(['member', 'volunteer', 'dokter', 'admin', 'superadmin'])],
            'role' => ['nullable', Rule::in(['member', 'volunteer', 'dokter', 'admin', 'superadmin'])],
        ]);

        $currentUser = Auth::user();

        // Prevent admin from changing their own role to prevent lockout
        if ($user->id === $currentUser->id) {
            return back()->with('error', 'Anda tidak dapat mengubah peran akun Anda sendiri.');
        }

        // Determine the assigned roles
        $selectedRoles = $validated['roles'] ?? [];
        if (empty($selectedRoles) && !empty($validated['role'])) {
            $selectedRoles = [$validated['role']];
        }

        // Always include 'member' so every account retains cat-owner workspace
        if (!in_array('member', $selectedRoles)) {
            $selectedRoles[] = 'member';
        }
        $selectedRoles = array_values(array_unique(array_map('strtolower', array_map('trim', $selectedRoles))));

        // Only superadmin can assign or revoke superadmin role
        if (in_array('superadmin', $selectedRoles) && !$currentUser->isSuperAdmin()) {
            return back()->with('error', 'Hanya Super Administrator yang dapat menetapkan peran Superadmin.');
        }
        if ($user->isSuperAdmin() && !in_array('superadmin', $selectedRoles) && !$currentUser->isSuperAdmin()) {
            return back()->with('error', 'Hanya Super Administrator yang dapat mencabut peran Superadmin.');
        }

        // If assigning superadmin, ensure admin is also included
        if (in_array('superadmin', $selectedRoles) && !in_array('admin', $selectedRoles)) {
            $selectedRoles[] = 'admin';
        }

        // Determine primary role column (for legacy backward compatibility)
        // Order of priority for primary role: superadmin > admin > dokter > volunteer > member
        $priorities = ['superadmin', 'admin', 'dokter', 'volunteer', 'member'];
        $primaryRole = 'member';
        foreach ($priorities as $p) {
            if (in_array($p, $selectedRoles)) {
                $primaryRole = $p;
                break;
            }
        }

        $user->roles = $selectedRoles;
        $user->role = $primaryRole;
        $user->save();

        $roleLabels = [
            'member' => 'Member (Pemilik Kucing)',
            'volunteer' => 'Relawan Sensus',
            'dokter' => 'Dokter Hewan',
            'admin' => 'Administrator',
            'superadmin' => 'Super Administrator',
        ];

        $assignedLabels = array_map(fn($r) => $roleLabels[$r] ?? ucfirst($r), $selectedRoles);
        $roleNamesStr = implode(', ', $assignedLabels);

        return back()->with('success', "Hak peran untuk {$user->name} berhasil diperbarui menjadi: {$roleNamesStr}. Pengguna kini dapat beralih ruang kerja sesuai perannya.");
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
