<?php

namespace App\Http\Controllers;

use App\Models\MasterWilayah;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MasterWilayahController extends Controller
{
    /**
     * Display a listing of the wilayah.
     */
    public function index(Request $request)
    {
        $query = MasterWilayah::withCount('cats');

        // Search filter
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('kode', 'like', "%{$search}%")
                  ->orWhere('nama', 'like', "%{$search}%")
                  ->orWhere('singkatan', 'like', "%{$search}%");
            });
        }

        // Status filter
        if ($request->has('status') && $request->status !== '' && $request->status !== null) {
            $query->where('is_active', (bool) $request->status);
        }

        $wilayahs = $query->orderBy('urutan', 'asc')
                          ->orderBy('kode', 'asc')
                          ->paginate(15)
                          ->withQueryString();

        $stats = [
            'total_wilayah' => MasterWilayah::count(),
            'active_wilayah' => MasterWilayah::where('is_active', true)->count(),
            'inactive_wilayah' => MasterWilayah::where('is_active', false)->count(),
            'total_cats_linked' => \App\Models\Cat::whereNotNull('wilayah_code')->count(),
        ];

        return view('admin.wilayah.index', compact('wilayahs', 'stats'));
    }

    /**
     * Store a newly created wilayah in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode' => ['required', 'string', 'max:10', 'unique:master_wilayahs,kode'],
            'nama' => ['required', 'string', 'max:255'],
            'singkatan' => ['nullable', 'string', 'max:50'],
            'urutan' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $validated['urutan'] = $validated['urutan'] ?? 0;
        $validated['is_active'] = $request->has('is_active') ? (bool) $request->is_active : true;

        $wilayah = MasterWilayah::create($validated);

        return redirect()->route('superadmin.wilayah.index')
            ->with('success', "Master Wilayah [{$wilayah->kode}] {$wilayah->nama} berhasil ditambahkan.");
    }

    /**
     * Update the specified wilayah in storage.
     */
    public function update(Request $request, MasterWilayah $wilayah)
    {
        $validated = $request->validate([
            'kode' => ['required', 'string', 'max:10', Rule::unique('master_wilayahs', 'kode')->ignore($wilayah->id)],
            'nama' => ['required', 'string', 'max:255'],
            'singkatan' => ['nullable', 'string', 'max:50'],
            'urutan' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $validated['urutan'] = $validated['urutan'] ?? 0;
        $validated['is_active'] = $request->has('is_active') ? (bool) $request->is_active : false;

        $wilayah->update($validated);

        return redirect()->route('superadmin.wilayah.index')
            ->with('success', "Master Wilayah [{$wilayah->kode}] {$wilayah->nama} berhasil diperbarui.");
    }

    /**
     * Remove the specified wilayah from storage.
     */
    public function destroy(MasterWilayah $wilayah)
    {
        $catsCount = $wilayah->cats()->count();

        if ($catsCount > 0) {
            return redirect()->route('superadmin.wilayah.index')
                ->with('error', "Wilayah [{$wilayah->kode}] {$wilayah->nama} tidak dapat dihapus karena terdapat {$catsCount} kucing terdaftar dengan kode ini. Silakan nonaktifkan wilayah jika sudah tidak digunakan.");
        }

        $nama = $wilayah->nama;
        $kode = $wilayah->kode;
        $wilayah->delete();

        return redirect()->route('superadmin.wilayah.index')
            ->with('success', "Master Wilayah [{$kode}] {$nama} berhasil dihapus.");
    }

    /**
     * Toggle active status of a wilayah.
     */
    public function toggleStatus(MasterWilayah $wilayah)
    {
        $wilayah->is_active = !$wilayah->is_active;
        $wilayah->save();

        $statusText = $wilayah->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return redirect()->route('superadmin.wilayah.index')
            ->with('success', "Status Wilayah [{$wilayah->kode}] {$wilayah->nama} berhasil {$statusText}.");
    }

    /**
     * Seed default 35 PWM Wilayahs.
     */
    public function seedDefault()
    {
        $seeder = new \Database\Seeders\MasterWilayahSeeder();
        $seeder->run();

        return redirect()->route('superadmin.wilayah.index')
            ->with('success', 'Data Master Wilayah default (35 PWM Muhammadiyah se-Indonesia) berhasil dimuat.');
    }
}
