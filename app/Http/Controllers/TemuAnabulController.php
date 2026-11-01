<?php

namespace App\Http\Controllers;

use App\Models\Cat;
use App\Models\TemuAnabul;
use App\Models\TemuAnabulPhoto;
use App\Models\TemuAnabulSighting;
use App\Services\ImageCompressionService;
use App\Services\IndonesiaWilayahService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TemuAnabulController extends Controller
{
    /**
     * Display Feed & Directory of Lost & Found Cats.
     */
    public function index(Request $request)
    {
        $query = TemuAnabul::with(['photos', 'user', 'cat']);

        // Tab Filter: All, Lost, Found, Reunited
        $type = $request->input('type');
        if ($type === 'lost' || $type === 'found') {
            $query->where('type', $type);
        }

        $status = $request->input('status');
        if ($status === 'reunited') {
            $query->where('status', 'reunited');
        } elseif ($status === 'active') {
            $query->where('status', 'active');
        }

        // Location Filters
        if ($request->filled('province')) {
            $query->where('province', $request->province);
        }
        if ($request->filled('city')) {
            $query->where('city', $request->city);
        }

        // Text Search
        if ($request->filled('q')) {
            $searchTerm = '%' . trim($request->q) . '%';
            $query->where(function ($q) use ($searchTerm) {
                $q->where('cat_name', 'like', $searchTerm)
                  ->orWhere('title', 'like', $searchTerm)
                  ->orWhere('coat_color', 'like', $searchTerm)
                  ->orWhere('coat_pattern', 'like', $searchTerm)
                  ->orWhere('distinctive_features', 'like', $searchTerm)
                  ->orWhere('city', 'like', $searchTerm)
                  ->orWhere('district', 'like', $searchTerm)
                  ->orWhere('formatted_address', 'like', $searchTerm);
            });
        }

        $reports = $query->latest('incident_date')->paginate(12)->withQueryString();

        // Statistics
        $stats = [
            'total' => TemuAnabul::count(),
            'lost' => TemuAnabul::where('type', 'lost')->where('status', 'active')->count(),
            'found' => TemuAnabul::where('type', 'found')->where('status', 'active')->count(),
            'reunited' => TemuAnabul::where('status', 'reunited')->count(),
        ];

        $provinces = IndonesiaWilayahService::getProvinces();

        return view('temu-anabul.index', compact('reports', 'stats', 'provinces'));
    }

    /**
     * Interactive GIS Map for Lost & Found Cats.
     */
    public function map(Request $request)
    {
        $provinces = IndonesiaWilayahService::getProvinces();
        $stats = [
            'total' => TemuAnabul::count(),
            'lost' => TemuAnabul::where('type', 'lost')->where('status', 'active')->count(),
            'found' => TemuAnabul::where('type', 'found')->where('status', 'active')->count(),
            'reunited' => TemuAnabul::where('status', 'reunited')->count(),
        ];

        return view('temu-anabul.map', compact('provinces', 'stats'));
    }

    /**
     * JSON Markers API for Temu Anabul map.
     */
    public function markers(Request $request)
    {
        $query = TemuAnabul::with(['photos', 'cat']);

        if ($request->filled('type') && in_array($request->type, ['lost', 'found'])) {
            $query->where('type', $request->type);
        }

        if ($request->filled('status') && in_array($request->status, ['active', 'reunited'])) {
            $query->where('status', $request->status);
        }

        if ($request->filled('province')) {
            $query->where('province', $request->province);
        }
        if ($request->filled('city')) {
            $query->where('city', $request->city);
        }

        if ($request->filled('q')) {
            $searchTerm = '%' . trim($request->q) . '%';
            $query->where(function ($q) use ($searchTerm) {
                $q->where('cat_name', 'like', $searchTerm)
                  ->orWhere('title', 'like', $searchTerm)
                  ->orWhere('coat_color', 'like', $searchTerm)
                  ->orWhere('distinctive_features', 'like', $searchTerm)
                  ->orWhere('city', 'like', $searchTerm);
            });
        }

        $items = $query->latest('incident_date')->limit(500)->get()->map(function ($report) {
            return [
                'id' => $report->id,
                'title' => $report->title,
                'cat_name' => $report->cat_name,
                'type' => $report->type,
                'type_label' => $report->type_label,
                'type_badge_class' => $report->type_badge_class,
                'status' => $report->status,
                'status_label' => $report->status_label,
                'lat' => (float) $report->latitude,
                'lng' => (float) $report->longitude,
                'search_radius_km' => (float) $report->search_radius_km,
                'thumbnail_url' => $report->thumbnail_url,
                'city' => $report->city ?: '',
                'formatted_address' => $report->formatted_address ?: '',
                'incident_date_human' => $report->incident_date ? $report->incident_date->translatedFormat('d M Y, H:i') : '-',
                'contact_name' => $report->contact_name,
                'contact_phone' => $report->is_contact_public ? $report->contact_phone : null,
                'whatsapp_url' => $report->is_contact_public ? $report->whatsapp_url : null,
                'reward_info' => $report->reward_info,
                'pin_visual' => $report->pin_visual,
                'detail_url' => route('temu-anabul.show', $report->id),
            ];
        });

        return response()->json([
            'status' => 'success',
            'count' => $items->count(),
            'data' => $items,
        ]);
    }

    /**
     * Show form to report a lost or found cat.
     */
    public function create(Request $request)
    {
        $user = Auth::user();
        $initialType = $request->query('type', 'lost');
        if (!in_array($initialType, ['lost', 'found'])) {
            $initialType = 'lost';
        }

        $myCats = $user ? Cat::where('user_id', $user->id)->whereNull('deleted_at')->get() : collect();
        $provinces = IndonesiaWilayahService::getProvinces();
        $regenciesMap = IndonesiaWilayahService::getRegenciesMap();

        $selectedCat = null;
        if ($request->filled('cat_id')) {
            $selectedCat = $myCats->firstWhere('id', (int) $request->cat_id);
        }

        return view('temu-anabul.create', compact('initialType', 'myCats', 'provinces', 'regenciesMap', 'selectedCat'));
    }

    /**
     * Store new lost or found cat report.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:lost,found',
            'title' => 'required|string|max:255',
            'cat_name' => 'required|string|max:255',
            'cat_id' => 'nullable|exists:cats,id',
            'gender' => 'required|in:jantan,betina,tidak_diketahui',
            'age_estimate' => 'nullable|string|max:100',
            'coat_pattern' => 'nullable|string|max:100',
            'coat_color' => 'nullable|string|max:100',
            'distinctive_features' => 'required|string|max:1000',
            'condition' => 'required|in:sehat,terawat,sakit,terluka,lemas_kelaparan,tidak_diketahui',
            'incident_date' => 'required|date',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'search_radius_km' => 'nullable|numeric|between:0.5,50',
            'province' => 'nullable|string|max:100',
            'city' => 'nullable|string|max:100',
            'district' => 'nullable|string|max:100',
            'village' => 'nullable|string|max:100',
            'formatted_address' => 'nullable|string|max:500',
            'contact_name' => 'required|string|max:255',
            'contact_phone' => 'required|string|max:30',
            'is_contact_public' => 'nullable|boolean',
            'reward_info' => 'nullable|string|max:255',
            'holding_status' => 'nullable|in:kept_by_finder,at_clinic_shelter,left_at_location,unknown',
            'notes' => 'nullable|string|max:2000',
            'photos' => 'nullable|array',
            'photos.*' => 'nullable|file|mimes:jpeg,png,jpg,webp|max:10240',
            'photos_cam' => 'nullable|array',
        ], [
            'type.required' => 'Pilih kategori laporan (Kucing Hilang atau Kucing Ditemukan).',
            'title.required' => 'Judul laporan wajib diisi.',
            'cat_name.required' => 'Nama anabul wajib diisi.',
            'distinctive_features.required' => 'Ciri fisik khas wajib dijelaskan agar mudah dikenali.',
            'incident_date.required' => 'Tanggal dan waktu kejadian wajib ditentukan.',
            'latitude.required' => 'Titik lokasi peta wajib ditentukan.',
            'contact_name.required' => 'Nama kontak pelapor wajib diisi.',
            'contact_phone.required' => 'Nomor WhatsApp atau telepon kontak wajib diisi.',
        ]);

        $user = Auth::user();
        $validated['user_id'] = $user->id;
        $validated['status'] = 'active';
        $validated['search_radius_km'] = $validated['search_radius_km'] ?? 3.0;
        $validated['is_contact_public'] = $request->boolean('is_contact_public', true);
        $validated['incident_date'] = date('Y-m-d H:i:s', strtotime($validated['incident_date']));

        $temuAnabul = TemuAnabul::create($validated);

        // Process Uploaded Photos
        $uploadedFiles = $request->file('photos', []);
        $cameraFiles = $request->input('photos_cam', []);
        $photoKeys = array_unique(array_merge(array_keys($uploadedFiles), array_keys($cameraFiles)));
        $isFirst = true;

        foreach ($photoKeys as $idx) {
            $rawFile = $uploadedFiles[$idx] ?? ($cameraFiles[$idx] ?? null);
            if (!$rawFile) continue;

            $savedPath = ImageCompressionService::compressAndStore($rawFile, 'temu-anabul');
            if ($savedPath) {
                TemuAnabulPhoto::create([
                    'temu_anabul_id' => $temuAnabul->id,
                    'photo_path' => $savedPath,
                    'is_primary' => $isFirst,
                ]);
                $isFirst = false;
            }
        }

        // If no photo was uploaded but linked to member pet cat, copy existing photo
        if ($isFirst && $temuAnabul->cat && $temuAnabul->cat->photo_path) {
            TemuAnabulPhoto::create([
                'temu_anabul_id' => $temuAnabul->id,
                'photo_path' => $temuAnabul->cat->photo_path,
                'is_primary' => true,
            ]);
        }

        $typeLabel = $temuAnabul->isLost() ? 'Kucing Hilang' : 'Kucing Ditemukan';
        return redirect()->route('temu-anabul.show', $temuAnabul->id)
            ->with('success', "Laporan {$typeLabel} berhasil dipublikasikan. Komunitas KucingMU akan ikut memantau.");
    }

    /**
     * Show report detail with smart matching recommendations & sightings.
     */
    public function show(TemuAnabul $temuAnabul)
    {
        $temuAnabul->load(['photos', 'user', 'cat', 'sightings.user']);
        
        // Smart Matching recommendations
        $potentialMatches = $temuAnabul->getPotentialMatches(4);

        return view('temu-anabul.show', compact('temuAnabul', 'potentialMatches'));
    }

    /**
     * Submit community sighting tip for a lost/found cat.
     */
    public function storeSighting(Request $request, TemuAnabul $temuAnabul)
    {
        $validated = $request->validate([
            'notes' => 'required|string|max:1500',
            'sighting_time' => 'nullable|date',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'location_name' => 'nullable|string|max:255',
            'contact_phone' => 'nullable|string|max:30',
            'photo' => 'nullable|file|mimes:jpeg,png,jpg,webp|max:10240',
        ], [
            'notes.required' => 'Keterangan penampakan atau jejak anabul wajib diisi.',
            'photo.max' => 'Ukuran foto maksimal 10 MB.',
        ]);

        $user = Auth::user();
        $savedPhotoPath = null;
        if ($request->hasFile('photo')) {
            $savedPhotoPath = ImageCompressionService::compressAndStore($request->file('photo'), 'temu-anabul-sightings');
        }

        TemuAnabulSighting::create([
            'temu_anabul_id' => $temuAnabul->id,
            'user_id' => $user->id,
            'notes' => $validated['notes'],
            'sighting_time' => !empty($validated['sighting_time']) ? date('Y-m-d H:i:s', strtotime($validated['sighting_time'])) : now(),
            'latitude' => $validated['latitude'] ?? null,
            'longitude' => $validated['longitude'] ?? null,
            'location_name' => $validated['location_name'] ?? null,
            'contact_phone' => $validated['contact_phone'] ?? null,
            'photo_path' => $savedPhotoPath,
        ]);

        return redirect()->route('temu-anabul.show', $temuAnabul->id)
            ->with('success', 'Informasi kesaksian Anda telah disampaikan kepada pelapor.');
    }

    /**
     * Mark a lost or found cat report as reunited.
     */
    public function markReunited(Request $request, TemuAnabul $temuAnabul)
    {
        $user = Auth::user();
        if (!$temuAnabul->canEditBy($user)) {
            abort(403, 'Hanya pelapor atau pengelola yang dapat memperbarui status laporan ini.');
        }

        $validated = $request->validate([
            'reunion_notes' => 'nullable|string|max:1500',
            'reunion_photo' => 'nullable|file|mimes:jpeg,png,jpg,webp|max:10240',
        ]);

        $savedPhoto = null;
        if ($request->hasFile('reunion_photo')) {
            $savedPhoto = ImageCompressionService::compressAndStore($request->file('reunion_photo'), 'temu-anabul-reunions');
        }

        $temuAnabul->update([
            'status' => 'reunited',
            'reunited_at' => now(),
            'reunion_notes' => $validated['reunion_notes'] ?? 'Alhamdulillah anabul telah berhasil kembali dan berkumpul bersama pemilik.',
            'reunion_photo_path' => $savedPhoto ?? $temuAnabul->reunion_photo_path,
        ]);

        return redirect()->route('temu-anabul.show', $temuAnabul->id)
            ->with('success', 'Kabar gembira! Laporan telah ditandai berhasil reuni.');
    }

    /**
     * Generate printable / shareable digital poster view.
     */
    public function poster(TemuAnabul $temuAnabul)
    {
        $temuAnabul->load(['photos', 'user']);
        return view('temu-anabul.poster', compact('temuAnabul'));
    }

    /**
     * Show edit form.
     */
    public function edit(TemuAnabul $temuAnabul)
    {
        $user = Auth::user();
        if (!$temuAnabul->canEditBy($user)) {
            abort(403, 'Anda tidak memiliki hak akses untuk mengubah laporan ini.');
        }

        $provinces = IndonesiaWilayahService::getProvinces();
        $regenciesMap = IndonesiaWilayahService::getRegenciesMap();
        $temuAnabul->load('photos');

        return view('temu-anabul.edit', compact('temuAnabul', 'provinces', 'regenciesMap'));
    }

    /**
     * Update report.
     */
    public function update(Request $request, TemuAnabul $temuAnabul)
    {
        $user = Auth::user();
        if (!$temuAnabul->canEditBy($user)) {
            abort(403, 'Anda tidak memiliki hak akses untuk mengubah laporan ini.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'cat_name' => 'required|string|max:255',
            'gender' => 'required|in:jantan,betina,tidak_diketahui',
            'age_estimate' => 'nullable|string|max:100',
            'coat_pattern' => 'nullable|string|max:100',
            'coat_color' => 'nullable|string|max:100',
            'distinctive_features' => 'required|string|max:1000',
            'condition' => 'required|in:sehat,terawat,sakit,terluka,lemas_kelaparan,tidak_diketahui',
            'status' => 'required|in:active,reunited,closed',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'province' => 'nullable|string|max:100',
            'city' => 'nullable|string|max:100',
            'district' => 'nullable|string|max:100',
            'village' => 'nullable|string|max:100',
            'formatted_address' => 'nullable|string|max:500',
            'contact_name' => 'required|string|max:255',
            'contact_phone' => 'required|string|max:30',
            'reward_info' => 'nullable|string|max:255',
            'holding_status' => 'nullable|in:kept_by_finder,at_clinic_shelter,left_at_location,unknown',
            'notes' => 'nullable|string|max:2000',
            'new_photos' => 'nullable|array',
            'new_photos.*' => 'nullable|file|mimes:jpeg,png,jpg,webp|max:10240',
        ]);

        $temuAnabul->update($validated);

        if ($request->hasFile('new_photos')) {
            $hasExistingPrimary = $temuAnabul->photos()->where('is_primary', true)->exists();
            foreach ($request->file('new_photos') as $idx => $photoFile) {
                $savedPath = ImageCompressionService::compressAndStore($photoFile, 'temu-anabul');
                if ($savedPath) {
                    TemuAnabulPhoto::create([
                        'temu_anabul_id' => $temuAnabul->id,
                        'photo_path' => $savedPath,
                        'is_primary' => !$hasExistingPrimary && $idx === 0,
                    ]);
                    $hasExistingPrimary = true;
                }
            }
        }

        return redirect()->route('temu-anabul.show', $temuAnabul->id)
            ->with('success', 'Laporan berhasil diperbarui.');
    }

    /**
     * Soft delete report.
     */
    public function destroy(TemuAnabul $temuAnabul)
    {
        $user = Auth::user();
        if (!$temuAnabul->canEditBy($user)) {
            abort(403, 'Anda tidak memiliki hak akses untuk menghapus laporan ini.');
        }

        $temuAnabul->delete();

        return redirect()->route('temu-anabul.index')
            ->with('success', 'Laporan berhasil dihapus.');
    }
}
