<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RepresentativeRegistration;
use App\Services\IndonesiaWilayahService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminRepresentativeController extends Controller
{
    /**
     * Display a listing of representative registrations.
     */
    public function index(Request $request)
    {
        $query = RepresentativeRegistration::with(['user', 'reviewer'])->latest();

        // Filter by Status
        $statusFilter = $request->get('status', 'all');
        if (in_array($statusFilter, ['pending', 'reviewed', 'approved', 'rejected'])) {
            $query->where('status', $statusFilter);
        }

        // Filter by Province
        if ($request->filled('province')) {
            $query->where('province_name', $request->province);
        }

        // Search query
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('registration_number', 'like', "%{$search}%")
                  ->orWhere('nbm', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('whatsapp_number', 'like', "%{$search}%")
                  ->orWhere('city_name', 'like', "%{$search}%")
                  ->orWhere('muhammadiyah_active_leadership', 'like', "%{$search}%");
            });
        }

        $representatives = $query->paginate(15)->withQueryString();

        $stats = [
            'total' => RepresentativeRegistration::count(),
            'pending' => RepresentativeRegistration::where('status', 'pending')->count(),
            'reviewed' => RepresentativeRegistration::where('status', 'reviewed')->count(),
            'approved' => RepresentativeRegistration::where('status', 'approved')->count(),
            'rejected' => RepresentativeRegistration::where('status', 'rejected')->count(),
            'total_provinces' => RepresentativeRegistration::distinct('province_name')->count('province_name'),
        ];

        // Fetch representative coordinates for Map Visualization based on filters
        $mapQuery = RepresentativeRegistration::query()
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->where('latitude', '!=', 0)
            ->where('longitude', '!=', 0);

        if (in_array($statusFilter, ['pending', 'reviewed', 'approved', 'rejected'])) {
            $mapQuery->where('status', $statusFilter);
        }

        if ($request->filled('province')) {
            $mapQuery->where('province_name', $request->province);
        }

        if ($request->filled('search')) {
            $search = trim($request->search);
            $mapQuery->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('registration_number', 'like', "%{$search}%")
                  ->orWhere('nbm', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('whatsapp_number', 'like', "%{$search}%")
                  ->orWhere('city_name', 'like', "%{$search}%")
                  ->orWhere('muhammadiyah_active_leadership', 'like', "%{$search}%");
            });
        }

        $mapRepresentatives = $mapQuery->get()->map(function ($item) {
            $badge = $item->status_badge;
            return [
                'id' => $item->id,
                'registration_number' => $item->registration_number,
                'name' => $item->name,
                'nbm' => $item->nbm ?: '-',
                'status' => $item->status,
                'status_label' => $item->status_label,
                'badge_bg' => $badge['bg'] ?? 'bg-slate-100 text-slate-800',
                'badge_dot' => $badge['dot'] ?? 'bg-slate-500',
                'province_name' => $item->province_name,
                'city_name' => $item->city_name,
                'district_name' => $item->district_name,
                'village_name' => $item->village_name,
                'latitude' => (float) $item->latitude,
                'longitude' => (float) $item->longitude,
                'formatted_address' => $item->formatted_address,
                'muhammadiyah_active_leadership' => $item->muhammadiyah_active_leadership,
                'whatsapp_number' => $item->whatsapp_number,
                'whatsapp_link' => $item->whatsapp_link,
                'show_url' => route('admin.representatives.show', $item->id),
                'created_at_formatted' => $item->created_at ? $item->created_at->format('d/m/Y') : '-',
            ];
        });

        $totalMapped = $mapRepresentatives->count();
        $totalOverallMapped = RepresentativeRegistration::whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->where('latitude', '!=', 0)
            ->where('longitude', '!=', 0)
            ->count();

        $provinces = IndonesiaWilayahService::getProvinces();

        return view('admin.representatives.index', compact(
            'representatives',
            'stats',
            'statusFilter',
            'provinces',
            'mapRepresentatives',
            'totalMapped',
            'totalOverallMapped'
        ));
    }

    /**
     * Display the specified representative details.
     */
    public function show(RepresentativeRegistration $representative)
    {
        $representative->load(['user', 'reviewer']);

        return view('admin.representatives.show', compact('representative'));
    }

    /**
     * Update the review status and admin notes for a representative.
     */
    public function updateStatus(Request $request, RepresentativeRegistration $representative)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,reviewed,approved,rejected',
            'admin_notes' => 'nullable|string|max:2000',
        ]);

        $representative->update([
            'status' => $validated['status'],
            'admin_notes' => $validated['admin_notes'],
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Status pendaftaran representatif ' . $representative->registration_number . ' berhasil diperbarui menjadi ' . $representative->status_label . '.');
    }

    /**
     * Remove the specified representative from storage.
     */
    public function destroy(RepresentativeRegistration $representative)
    {
        // Delete uploaded files if exist
        if ($representative->sk_pimpinan_document_path && Storage::disk('public')->exists($representative->sk_pimpinan_document_path)) {
            Storage::disk('public')->delete($representative->sk_pimpinan_document_path);
        }
        if ($representative->ktam_document_path && Storage::disk('public')->exists($representative->ktam_document_path)) {
            Storage::disk('public')->delete($representative->ktam_document_path);
        }

        $repNumber = $representative->registration_number;
        $representative->delete();

        return redirect()->route('admin.representatives.index')
            ->with('success', "Data pendaftaran representatif {$repNumber} berhasil dihapus.");
    }

    /**
     * Export representative data as CSV.
     */
    public function export(Request $request): StreamedResponse
    {
        $query = RepresentativeRegistration::latest();

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }
        if ($request->filled('province')) {
            $query->where('province_name', $request->province);
        }

        $filename = 'data-representatif-kucingmu-' . date('Y-m-d-His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        return response()->stream(function () use ($query) {
            $handle = fopen('php://output', 'w');
            
            // Add UTF-8 BOM for Excel Indonesian compatibility
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // CSV Header
            fputcsv($handle, [
                'No. Registrasi',
                'Nama Lengkap',
                'NBM',
                'Tanggal Lahir',
                'Email',
                'WhatsApp',
                'Instagram',
                'Provinsi',
                'Kota/Kabupaten',
                'Kecamatan',
                'Desa/Kelurahan',
                'Koordinat Lat',
                'Koordinat Lng',
                'Alamat Terformat',
                'Pimpinan Muhammadiyah/Ortom Aktif',
                'Status',
                'Catatan Admin',
                'Tanggal Pendaftaran',
            ]);

            $query->chunk(100, function ($rows) use ($handle) {
                foreach ($rows as $r) {
                    fputcsv($handle, [
                        $r->registration_number,
                        $r->name,
                        $r->nbm ?? '-',
                        $r->birth_date ? $r->birth_date->format('Y-m-d') : '-',
                        $r->email,
                        $r->whatsapp_number,
                        $r->instagram_username ? '@' . $r->instagram_username : '-',
                        $r->province_name,
                        $r->city_name,
                        $r->district_name,
                        $r->village_name,
                        $r->latitude ?: '-',
                        $r->longitude ?: '-',
                        $r->formatted_address ?: '-',
                        $r->muhammadiyah_active_leadership,
                        $r->status_label,
                        $r->admin_notes ?: '-',
                        $r->created_at ? $r->created_at->format('d/m/Y H:i') : '-',
                    ]);
                }
            });

            fclose($handle);
        }, 200, $headers);
    }
}
