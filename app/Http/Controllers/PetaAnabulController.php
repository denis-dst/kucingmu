<?php

namespace App\Http\Controllers;

use App\Models\Cat;
use App\Models\PetaAnabul;
use App\Models\PetaAnabulPhoto;
use App\Models\PetaAnabulUpdate;
use App\Services\ImageCompressionService;
use App\Services\IndonesiaWilayahService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PetaAnabulController extends Controller
{
    /**
     * Ensure SQLite database has trigonometric functions registered if testing or using SQLite.
     */
    protected function ensureSqliteTrigFunctions(): void
    {
        try {
            $connection = DB::connection();
            if ($connection->getDriverName() === 'sqlite') {
                $pdo = $connection->getPdo();
                if (method_exists($pdo, 'sqliteCreateFunction')) {
                    $pdo->sqliteCreateFunction('acos', fn ($v) => acos(min(1.0, max(-1.0, (float) $v))), 1);
                    $pdo->sqliteCreateFunction('cos', fn ($v) => cos((float) $v), 1);
                    $pdo->sqliteCreateFunction('sin', fn ($v) => sin((float) $v), 1);
                    $pdo->sqliteCreateFunction('radians', fn ($v) => deg2rad((float) $v), 1);
                }
            }
        } catch (\Throwable $e) {
            // Ignore if already registered or driver does not support
        }
    }

    /**
     * Display GIS Map & List view of mapped cats.
     */
    public function index(Request $request)
    {
        $this->ensureSqliteTrigFunctions();

        $query = PetaAnabul::with(['photos', 'user', 'cat'])->where('status', '!=', 'arsip');

        // Filter: Tipe Kucing (Peliharaan vs Liar)
        if ($request->filled('ownership_type') && in_array($request->ownership_type, ['pet', 'stray'])) {
            $query->where('ownership_type', $request->ownership_type);
        }

        // Filter: Kondisi
        if ($request->filled('condition') && $request->condition !== 'all') {
            $query->where('condition', $request->condition);
        }

        // Filter: Jenis Kelamin
        if ($request->filled('gender') && $request->gender !== 'all') {
            $query->where('gender', $request->gender);
        }

        // Filter: Status Steril
        if ($request->filled('sterilization_status') && $request->sterilization_status !== 'all') {
            $query->where('sterilization_status', $request->sterilization_status);
        }

        // Filter: Wilayah (Provinsi & Kota)
        if ($request->filled('province')) {
            $query->where('province', $request->province);
        }
        if ($request->filled('city')) {
            $query->where('city', $request->city);
        }

        // Filter: Pencarian Teks
        if ($request->filled('q')) {
            $searchTerm = '%' . trim($request->q) . '%';
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', $searchTerm)
                  ->orWhere('unique_code', 'like', $searchTerm)
                  ->orWhere('coat_color', 'like', $searchTerm)
                  ->orWhere('coat_pattern', 'like', $searchTerm)
                  ->orWhere('characteristics', 'like', $searchTerm)
                  ->orWhere('formatted_address', 'like', $searchTerm)
                  ->orWhere('city', 'like', $searchTerm)
                  ->orWhere('district', 'like', $searchTerm);
            });
        }

        // Filter: Radius Geografis (Haversine formula in KM)
        $userLat = $request->input('lat');
        $userLng = $request->input('lng');
        $radiusKm = $request->input('radius');

        if ($userLat !== null && $userLng !== null && is_numeric($userLat) && is_numeric($userLng) && is_numeric($radiusKm) && $radiusKm > 0) {
            $haversine = "(6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude))))";
            $query->select('peta_anabuls.*')
                  ->selectRaw("{$haversine} AS distance_km", [$userLat, $userLng, $userLat])
                  ->whereRaw("{$haversine} <= ?", [$userLat, $userLng, $userLat, (float)$radiusKm])
                  ->orderBy('distance_km', 'asc');
        } else {
            $query->latest('id');
        }

        // Statistics Summary
        $stats = [
            'total' => PetaAnabul::where('status', '!=', 'arsip')->count(),
            'pets' => PetaAnabul::where('ownership_type', 'pet')->where('status', '!=', 'arsip')->count(),
            'strays' => PetaAnabul::where('ownership_type', 'stray')->where('status', '!=', 'arsip')->count(),
            'needs_help' => PetaAnabul::whereIn('condition', ['sakit', 'terluka', 'butuh_pertolongan'])->where('status', '!=', 'arsip')->count(),
        ];

        $provinces = IndonesiaWilayahService::getProvinces();
        $viewMode = $request->input('view', 'map'); // 'map' or 'list'

        if ($viewMode === 'list') {
            $cats = $query->paginate(15)->withQueryString();
            return view('peta-anabul.index', compact('cats', 'stats', 'provinces', 'viewMode'));
        }

        // Map View: fetch items for map initialization
        $cats = $query->limit(500)->get();

        return view('peta-anabul.index', compact('cats', 'stats', 'provinces', 'viewMode'));
    }

    /**
     * API JSON endpoint for interactive map markers.
     */
    public function markers(Request $request)
    {
        $this->ensureSqliteTrigFunctions();

        $query = PetaAnabul::with(['photos', 'cat'])->where('status', '!=', 'arsip');

        if ($request->filled('ownership_type') && in_array($request->ownership_type, ['pet', 'stray'])) {
            $query->where('ownership_type', $request->ownership_type);
        }

        if ($request->filled('condition') && $request->condition !== 'all') {
            $query->where('condition', $request->condition);
        }

        if ($request->filled('gender') && $request->gender !== 'all') {
            $query->where('gender', $request->gender);
        }

        if ($request->filled('sterilization_status') && $request->sterilization_status !== 'all') {
            $query->where('sterilization_status', $request->sterilization_status);
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
                $q->where('name', 'like', $searchTerm)
                  ->orWhere('unique_code', 'like', $searchTerm)
                  ->orWhere('coat_color', 'like', $searchTerm)
                  ->orWhere('coat_pattern', 'like', $searchTerm)
                  ->orWhere('formatted_address', 'like', $searchTerm)
                  ->orWhere('city', 'like', $searchTerm);
            });
        }

        $userLat = $request->input('lat');
        $userLng = $request->input('lng');
        $radiusKm = $request->input('radius');

        if ($userLat !== null && $userLng !== null && is_numeric($userLat) && is_numeric($userLng) && is_numeric($radiusKm) && $radiusKm > 0) {
            $haversine = "(6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude))))";
            $query->select('peta_anabuls.*')
                  ->selectRaw("{$haversine} AS distance_km", [$userLat, $userLng, $userLat])
                  ->whereRaw("{$haversine} <= ?", [$userLat, $userLng, $userLat, (float)$radiusKm])
                  ->orderBy('distance_km', 'asc');
        } else {
            $query->latest('id');
        }

        $items = $query->limit(800)->get()->map(function ($cat) {
            $pinVisual = $cat->pin_visual;
            return [
                'id' => $cat->id,
                'name' => $cat->name,
                'unique_code' => $cat->unique_code,
                'ownership_type' => $cat->ownership_type,
                'ownership_label' => $cat->ownership_label,
                'lat' => (float) $cat->latitude,
                'lng' => (float) $cat->longitude,
                'condition' => $cat->condition,
                'condition_label' => $cat->condition_label,
                'condition_badge_class' => $cat->condition_badge_class,
                'status' => $cat->status,
                'status_label' => $cat->status_label,
                'gender' => $cat->gender,
                'coat_pattern' => $cat->coat_pattern ?: '-',
                'coat_color' => $cat->coat_color ?: '-',
                'age_estimate' => $cat->age_estimate ?: '-',
                'thumbnail_url' => $cat->thumbnail_url,
                'city' => $cat->city ?: '',
                'formatted_address' => $cat->formatted_address ?: '',
                'last_seen_at_human' => $cat->last_seen_at ? $cat->last_seen_at->translatedFormat('d M Y, H:i') : '-',
                'pin_visual' => $pinVisual,
                'detail_url' => route('peta-anabul.show', $cat->id),
                'distance_km' => isset($cat->distance_km) ? round($cat->distance_km, 2) : null,
            ];
        });

        return response()->json([
            'status' => 'success',
            'count' => $items->count(),
            'data' => $items,
        ]);
    }

    /**
     * Show form to log a new cat into Peta Anabul.
     */
    public function create(Request $request)
    {
        $user = Auth::user();
        $myCats = $user ? Cat::where('user_id', $user->id)->whereNull('deleted_at')->get() : collect();
        $provinces = IndonesiaWilayahService::getProvinces();
        $regenciesMap = IndonesiaWilayahService::getRegenciesMap();

        // Selected cat from KTAM query param if applicable
        $selectedCat = null;
        if ($request->filled('cat_id')) {
            $selectedCat = $myCats->firstWhere('id', (int) $request->cat_id);
        }

        return view('peta-anabul.create', compact('myCats', 'provinces', 'regenciesMap', 'selectedCat'));
    }

    /**
     * Store a newly created cat mapping.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'ownership_type' => 'required|in:pet,stray',
            'name' => 'required|string|max:255',
            'cat_id' => 'nullable|exists:cats,id',
            'coat_pattern' => 'nullable|string|max:100',
            'coat_color' => 'nullable|string|max:100',
            'gender' => 'required|in:jantan,betina,tidak_diketahui',
            'age_estimate' => 'nullable|string|max:100',
            'characteristics' => 'nullable|string|max:1000',
            'condition' => 'required|in:sehat,sakit,terluka,butuh_pertolongan,tidak_diketahui',
            'sterilization_status' => 'required|in:sudah,belum,tidak_diketahui',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'province' => 'nullable|string|max:100',
            'city' => 'nullable|string|max:100',
            'district' => 'nullable|string|max:100',
            'village' => 'nullable|string|max:100',
            'formatted_address' => 'nullable|string|max:500',
            'last_seen_at' => 'nullable|date',
            'description' => 'nullable|string|max:2000',
            'photos' => 'nullable|array',
            'photos.*' => 'nullable|file|mimes:jpeg,png,jpg,webp|max:10240',
            'photos_cam' => 'nullable|array',
        ], [
            'ownership_type.required' => 'Pilih kategori anabul (Peliharaan atau Liar di sekitar).',
            'name.required' => 'Nama panggilan atau kode anabul wajib diisi.',
            'latitude.required' => 'Titik lokasi peta wajib ditentukan.',
            'longitude.required' => 'Titik lokasi peta wajib ditentukan.',
            'photos.*.max' => 'Ukuran setiap foto maksimal 10 MB.',
        ]);

        $user = Auth::user();
        $validated['user_id'] = $user->id;
        $validated['unique_code'] = PetaAnabul::generateUniqueCode();
        $validated['last_seen_at'] = !empty($validated['last_seen_at']) ? date('Y-m-d H:i:s', strtotime($validated['last_seen_at'])) : now();
        $validated['status'] = 'aktif';
        $validated['is_verified'] = true;
        $validated['moderation_status'] = 'approved';

        $petaAnabul = PetaAnabul::create($validated);

        // Process Uploaded Photos (File upload or Camera base64)
        $uploadedFiles = $request->file('photos', []);
        $cameraFiles = $request->input('photos_cam', []);
        $photoKeys = array_unique(array_merge(array_keys($uploadedFiles), array_keys($cameraFiles)));
        $isFirst = true;

        foreach ($photoKeys as $idx) {
            $rawFile = $uploadedFiles[$idx] ?? ($cameraFiles[$idx] ?? null);
            if (!$rawFile) continue;

            $savedPath = ImageCompressionService::compressAndStore($rawFile, 'peta-anabul');
            if ($savedPath) {
                PetaAnabulPhoto::create([
                    'peta_anabul_id' => $petaAnabul->id,
                    'photo_path' => $savedPath,
                    'caption' => 'Foto ' . ($idx + 1),
                    'is_primary' => $isFirst,
                ]);
                $isFirst = false;
            }
        }

        // If no photo uploaded but linked to member pet cat, copy existing cat photo path
        if ($isFirst && $petaAnabul->cat && $petaAnabul->cat->photo_path) {
            PetaAnabulPhoto::create([
                'peta_anabul_id' => $petaAnabul->id,
                'photo_path' => $petaAnabul->cat->photo_path,
                'caption' => 'Foto Kucing Saya',
                'is_primary' => true,
            ]);
        }

        return redirect()->route('peta-anabul.show', $petaAnabul->id)
            ->with('success', 'Data anabul berhasil dipetakan ke Peta Anabul.');
    }

    /**
     * Show detail view of a mapped cat.
     */
    public function show(PetaAnabul $petaAnabul)
    {
        $this->ensureSqliteTrigFunctions();

        $petaAnabul->load(['photos', 'user', 'cat', 'updates.user', 'updates.moderator']);
        
        // Nearby cats within 5km for context
        $haversine = "(6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude))))";
        $nearbyCats = PetaAnabul::with('photos')
            ->select('peta_anabuls.*')
            ->selectRaw("{$haversine} AS distance_km", [$petaAnabul->latitude, $petaAnabul->longitude, $petaAnabul->latitude])
            ->where('id', '!=', $petaAnabul->id)
            ->where('status', '!=', 'arsip')
            ->whereRaw("{$haversine} <= 10", [$petaAnabul->latitude, $petaAnabul->longitude, $petaAnabul->latitude])
            ->orderBy('distance_km', 'asc')
            ->take(4)
            ->get();

        return view('peta-anabul.show', compact('petaAnabul', 'nearbyCats'));
    }

    /**
     * Store community contribution / update for a mapped cat.
     */
    public function storeUpdate(Request $request, PetaAnabul $petaAnabul)
    {
        $validated = $request->validate([
            'update_type' => 'required|in:sighting,condition_update,handled,not_seen,correction,duplicate_report,report_inappropriate',
            'notes' => 'required|string|max:1500',
            'condition' => 'nullable|in:sehat,sakit,terluka,butuh_pertolongan,tidak_diketahui',
            'status' => 'nullable|in:aktif,sudah_ditangani,tidak_terlihat_lagi,arsip',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'photo' => 'nullable|file|mimes:jpeg,png,jpg,webp|max:10240',
        ], [
            'update_type.required' => 'Pilih jenis kontribusi informasi.',
            'notes.required' => 'Catatan atau keterangan wajib diisi.',
            'photo.max' => 'Ukuran foto maksimal 10 MB.',
        ]);

        $user = Auth::user();
        $savedPhotoPath = null;
        if ($request->hasFile('photo')) {
            $savedPhotoPath = ImageCompressionService::compressAndStore($request->file('photo'), 'peta-anabul-updates');
        }

        $update = PetaAnabulUpdate::create([
            'peta_anabul_id' => $petaAnabul->id,
            'user_id' => $user->id,
            'update_type' => $validated['update_type'],
            'notes' => $validated['notes'],
            'condition' => $validated['condition'] ?? null,
            'status' => $validated['status'] ?? null,
            'latitude' => $validated['latitude'] ?? null,
            'longitude' => $validated['longitude'] ?? null,
            'photo_path' => $savedPhotoPath,
            'moderation_status' => 'approved',
        ]);

        // Auto-update parent PetaAnabul condition or status when relevant
        if (!empty($validated['condition'])) {
            $petaAnabul->update(['condition' => $validated['condition']]);
        }
        if (!empty($validated['status'])) {
            $petaAnabul->update(['status' => $validated['status']]);
        }
        if ($validated['update_type'] === 'handled') {
            $petaAnabul->update(['status' => 'sudah_ditangani']);
        } elseif ($validated['update_type'] === 'not_seen') {
            $petaAnabul->update(['status' => 'tidak_terlihat_lagi']);
        }

        return redirect()->route('peta-anabul.show', $petaAnabul->id)
            ->with('success', 'Terima kasih. Kontribusi informasi Anda berhasil ditambahkan ke riwayat anabul.');
    }

    /**
     * Show edit form for cat mapping.
     */
    public function edit(PetaAnabul $petaAnabul)
    {
        $user = Auth::user();
        if (!$petaAnabul->canEditBy($user)) {
            abort(403, 'Anda tidak memiliki hak akses untuk mengubah data anabul ini.');
        }

        $provinces = IndonesiaWilayahService::getProvinces();
        $regenciesMap = IndonesiaWilayahService::getRegenciesMap();
        $petaAnabul->load('photos');

        return view('peta-anabul.edit', compact('petaAnabul', 'provinces', 'regenciesMap'));
    }

    /**
     * Update cat mapping entry.
     */
    public function update(Request $request, PetaAnabul $petaAnabul)
    {
        $user = Auth::user();
        if (!$petaAnabul->canEditBy($user)) {
            abort(403, 'Anda tidak memiliki hak akses untuk mengubah data anabul ini.');
        }

        $validated = $request->validate([
            'ownership_type' => 'required|in:pet,stray',
            'name' => 'required|string|max:255',
            'coat_pattern' => 'nullable|string|max:100',
            'coat_color' => 'nullable|string|max:100',
            'gender' => 'required|in:jantan,betina,tidak_diketahui',
            'age_estimate' => 'nullable|string|max:100',
            'characteristics' => 'nullable|string|max:1000',
            'condition' => 'required|in:sehat,sakit,terluka,butuh_pertolongan,tidak_diketahui',
            'sterilization_status' => 'required|in:sudah,belum,tidak_diketahui',
            'status' => 'required|in:aktif,sudah_ditangani,tidak_terlihat_lagi,arsip',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'province' => 'nullable|string|max:100',
            'city' => 'nullable|string|max:100',
            'district' => 'nullable|string|max:100',
            'village' => 'nullable|string|max:100',
            'formatted_address' => 'nullable|string|max:500',
            'last_seen_at' => 'nullable|date',
            'description' => 'nullable|string|max:2000',
            'new_photos' => 'nullable|array',
            'new_photos.*' => 'nullable|file|mimes:jpeg,png,jpg,webp|max:10240',
        ]);

        $validated['last_seen_at'] = !empty($validated['last_seen_at']) ? date('Y-m-d H:i:s', strtotime($validated['last_seen_at'])) : $petaAnabul->last_seen_at;

        $petaAnabul->update($validated);

        // Upload additional photos if any
        if ($request->hasFile('new_photos')) {
            $hasExistingPrimary = $petaAnabul->photos()->where('is_primary', true)->exists();
            foreach ($request->file('new_photos') as $idx => $photoFile) {
                $savedPath = ImageCompressionService::compressAndStore($photoFile, 'peta-anabul');
                if ($savedPath) {
                    PetaAnabulPhoto::create([
                        'peta_anabul_id' => $petaAnabul->id,
                        'photo_path' => $savedPath,
                        'caption' => 'Foto Tambahan',
                        'is_primary' => !$hasExistingPrimary && $idx === 0,
                    ]);
                    $hasExistingPrimary = true;
                }
            }
        }

        return redirect()->route('peta-anabul.show', $petaAnabul->id)
            ->with('success', 'Data anabul berhasil diperbarui.');
    }

    /**
     * Soft delete a mapped cat entry.
     */
    public function destroy(PetaAnabul $petaAnabul)
    {
        $user = Auth::user();
        if (!$petaAnabul->canEditBy($user)) {
            abort(403, 'Anda tidak memiliki hak akses untuk menghapus data anabul ini.');
        }

        $petaAnabul->delete();

        return redirect()->route('peta-anabul.index')
            ->with('success', 'Data anabul berhasil dihapus.');
    }
}
