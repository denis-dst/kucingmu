<?php

namespace App\Http\Controllers;

use App\Models\PtmaCatCensus;
use App\Models\Cat;
use App\Models\CatPhoto;
use App\Models\StrayCatSurvey;
use App\Services\CatBiometricService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class PtmaCatCensusController extends Controller
{
    /**
     * Display a listing of the census records.
     */
    public function index(Request $request)
    {
        $query = PtmaCatCensus::with('volunteer')->latest();

        if ($request->filled('kampus')) {
            $query->where('kampus', $request->kampus);
        }

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('id_kucing', 'LIKE', "%{$search}%")
                  ->orWhere('zona', 'LIKE', "%{$search}%")
                  ->orWhere('warna', 'LIKE', "%{$search}%")
                  ->orWhere('warna_custom', 'LIKE', "%{$search}%")
                  ->orWhere('kampus_custom', 'LIKE', "%{$search}%");
            });
        }

        if ($request->filled('bcs')) {
            $query->where('bcs', $request->bcs);
        }

        $censuses = $query->paginate(12)->withQueryString();

        $campusCounts = PtmaCatCensus::selectRaw("
            COUNT(*) as total,
            SUM(CASE WHEN kampus = 'UMY' THEN 1 ELSE 0 END) as umy,
            SUM(CASE WHEN kampus = 'UAD' THEN 1 ELSE 0 END) as uad,
            SUM(CASE WHEN kampus = 'UMP' THEN 1 ELSE 0 END) as ump,
            SUM(CASE WHEN kampus = 'UMS' THEN 1 ELSE 0 END) as ums,
            SUM(CASE WHEN volunteer_id = ? THEN 1 ELSE 0 END) as my_submissions
        ", [Auth::id() ?? 0])->first();

        $stats = [
            'total'          => (int) ($campusCounts->total ?? 0),
            'umy'            => (int) ($campusCounts->umy ?? 0),
            'uad'            => (int) ($campusCounts->uad ?? 0),
            'ump'            => (int) ($campusCounts->ump ?? 0),
            'ums'            => (int) ($campusCounts->ums ?? 0),
            'my_submissions' => (int) ($campusCounts->my_submissions ?? 0),
        ];

        return view('volunteer.census.index', compact('censuses', 'stats'));
    }

    /**
     * Show the dedicated scanner view to identify if a cat has already been censused.
     */
    public function scan(Request $request)
    {
        $defaultZones = [
            'UMY - Selatan',
            'UMY - Utara',
            'UMY - Tengah (admisi, AR, maskam, boga)',
            'Unires & E8',
        ];
        $dbZones = PtmaCatCensus::whereNotNull('zona')->pluck('zona')->toArray();
        $zones = array_values(array_unique(array_filter(array_merge($defaultZones, $dbZones))));

        $totalRegistered = PtmaCatCensus::count() + Cat::count() + StrayCatSurvey::whereNotNull('photo_path')->count();
        $campuses = ['UMY', 'UAD', 'UMP', 'UMS'];

        return view('volunteer.census.scan', compact('zones', 'totalRegistered', 'campuses'));
    }

    /**
     * Match a scanned cat embedding / photo against master records across all cat modules.
     */
    public function match(Request $request)
    {
        $request->validate([
            'embedding'     => 'nullable|array',
            'embedding.*'   => 'numeric',
            'image_base64'  => 'nullable|string',
            'image_file'    => 'nullable|image|max:8192',
            'kampus'        => 'nullable|string|max:50',
            'warna'         => 'nullable|string|max:50',
            'threshold'     => 'nullable|numeric|min:0|max:1',
        ]);

        $queryEmbedding = $request->input('embedding');
        $rawBinary = null;

        if ($request->hasFile('image_file')) {
            $rawBinary = file_get_contents($request->file('image_file')->getPathname());
        } elseif ($request->filled('image_base64')) {
            $b64 = $request->input('image_base64');
            if (preg_match('/^data:image\/(\w+);base64,/', $b64)) {
                $b64 = substr($b64, strpos($b64, ',') + 1);
            }
            $rawBinary = base64_decode($b64) ?: null;
        }

        $querySpatial = null;
        $queryColor = null;
        $queryHash = null;

        if ($rawBinary) {
            $querySpatial = CatBiometricService::extractSpatialFingerprint($rawBinary);
            $queryColor = CatBiometricService::extractColorFingerprint($rawBinary);
            $queryHash = CatBiometricService::extractDHash($rawBinary);
        }

        if (empty($queryEmbedding) && empty($querySpatial) && empty($queryColor)) {
            return response()->json([
                'success' => false,
                'message' => 'Foto atau data visual biometrik wajib disertakan untuk pemindaian.',
            ], 422);
        }

        $queryBiometrics = [
            'embedding' => $queryEmbedding,
            'spatial'   => $querySpatial,
            'color'     => $queryColor,
            'hash'      => $queryHash,
        ];

        $minThreshold = (float) $request->input('threshold', 0.40);
        $candidates = [];
        $totalCompared = 0;

        // 1. Match with PTMA Cat Census (ptma_cat_censuses) - Multi-Angle Matching
        $censusQuery = PtmaCatCensus::query();
        if ($request->filled('kampus') && $request->kampus !== 'Semua') {
            $censusQuery->where('kampus', $request->kampus);
        }
        $censuses = $censusQuery->get();
        $totalCompared += $censuses->count();

        foreach ($censuses as $census) {
            $slots = [
                'wajah'    => ['path' => $census->foto_wajah, 'label' => 'Foto Wajah', 'emb' => $census->foto_wajah_embedding ?? ($census->multi_embeddings['wajah'] ?? null)],
                'atas'     => ['path' => $census->foto_atas, 'label' => 'Tampak Atas / Punggung', 'emb' => $census->multi_embeddings['atas'] ?? null],
                'samping'  => ['path' => $census->foto_samping_kiri, 'label' => 'Samping Kiri', 'emb' => $census->multi_embeddings['samping'] ?? null],
                'opsional' => ['path' => $census->foto_opsional, 'label' => 'Ciri Unik / Tambahan', 'emb' => $census->multi_embeddings['opsional'] ?? null],
            ];

            $spatialFp = is_array($census->spatial_fingerprint) ? $census->spatial_fingerprint : [];
            $bestSlotEval = null;
            $bestSlotKey = 'wajah';
            $bestSlotScore = 0.0;

            foreach ($slots as $slotKey => $slotData) {
                if (empty($slotData['path'])) continue;

                // Load or extract target fingerprints
                $targetFps = $spatialFp[$slotKey] ?? null;
                if (!$targetFps) {
                    $targetFps = CatBiometricService::getOrGenerateFingerprints($slotData['path']);
                    if ($targetFps) {
                        $spatialFp[$slotKey] = $targetFps;
                    }
                }

                $targetBiometrics = [
                    'embedding' => $slotData['emb'],
                    'spatial'   => $targetFps['spatial'] ?? null,
                    'color'     => $targetFps['color'] ?? ($slotKey === 'wajah' ? $census->color_fingerprint : null),
                    'hash'      => $targetFps['hash'] ?? null,
                ];

                $eval = CatBiometricService::evaluateMatchScore($queryBiometrics, $targetBiometrics);
                if ($eval['final_score'] > $bestSlotScore) {
                    $bestSlotScore = $eval['final_score'];
                    $bestSlotEval = $eval;
                    $bestSlotKey = $slotKey;
                }
            }

            // Save back cached fingerprints if newly generated
            if ($spatialFp !== $census->spatial_fingerprint) {
                $census->spatial_fingerprint = $spatialFp;
                $census->saveQuietly();
            }

            if (!$bestSlotEval || $bestSlotScore < $minThreshold) {
                continue;
            }

            // Contextual adjustments
            $finalScore = $bestSlotScore;
            if ($request->filled('warna') && $request->warna !== 'Lainnya' && $census->warna === $request->warna) {
                $finalScore = min(1.0, $finalScore + 0.04);
            }
            if ($request->filled('kampus') && $request->kampus !== 'Semua' && $census->kampus === $request->kampus) {
                $finalScore = min(1.0, $finalScore + 0.02);
            }

            $matchedPhotoUrl = match($bestSlotKey) {
                'atas'     => $census->foto_atas_url,
                'samping'  => $census->foto_samping_kiri_url,
                'opsional' => $census->foto_opsional_url,
                default    => $census->foto_wajah_url,
            } ?: $census->foto_wajah_url;

            $candidates[] = [
                'source_type'           => 'census',
                'source_label'          => 'Sensus PTMA',
                'id'                    => $census->id,
                'id_kucing'             => $census->id_kucing,
                'kampus'                => $census->kampus,
                'display_kampus'        => $census->display_kampus,
                'zona'                  => $census->zona,
                'usia'                  => $census->usia,
                'gender'                => $census->gender,
                'warna'                 => $census->warna,
                'display_warna'         => $census->display_warna,
                'bcs'                   => $census->bcs,
                'foto_wajah_url'        => $matchedPhotoUrl,
                'matched_angle'         => $slots[$bestSlotKey]['label'] ?? 'Foto Wajah',
                'detail_url'            => route('volunteer.census.show', $census->id),
                'created_at_formatted'  => $census->created_at ? $census->created_at->format('d M Y, H:i') : '-',
                'similarity'            => round($finalScore, 4),
                'similarity_percent'    => round($finalScore * 100, 1),
                'metrics'               => [
                    'deep'    => $bestSlotEval['deep_score'],
                    'spatial' => $bestSlotEval['spatial_score'],
                    'color'   => $bestSlotEval['color_score'],
                    'hash'    => $bestSlotEval['hash_score'],
                ],
            ];
        }

        // 2. Match with Member Cats / KTAM (cats & cat_photos table)
        if (!$request->filled('kampus') || $request->kampus === 'Semua') {
            $memberCats = Cat::with(['owner', 'photos'])->whereNotNull('photo_path')->get();
            $totalCompared += $memberCats->count();

            foreach ($memberCats as $cat) {
                $bestCatEval = null;
                $bestCatScore = 0.0;
                $bestPhotoUrl = $cat->primary_photo_url;
                $bestPhotoLabel = 'Foto Profil KTAM';

                // Check primary photo
                $primaryFps = $cat->spatial_fingerprint ?: CatBiometricService::getOrGenerateFingerprints($cat->photo_path);
                if ($primaryFps && empty($cat->spatial_fingerprint)) {
                    $cat->spatial_fingerprint = $primaryFps;
                    $cat->color_fingerprint = $primaryFps['color'];
                    $cat->saveQuietly();
                }

                if ($primaryFps) {
                    $eval = CatBiometricService::evaluateMatchScore($queryBiometrics, [
                        'embedding' => $cat->photo_embedding,
                        'spatial'   => $primaryFps['spatial'] ?? null,
                        'color'     => $primaryFps['color'] ?? $cat->color_fingerprint,
                        'hash'      => $primaryFps['hash'] ?? null,
                    ]);
                    if ($eval['final_score'] > $bestCatScore) {
                        $bestCatScore = $eval['final_score'];
                        $bestCatEval = $eval;
                    }
                }

                // Check gallery photos (CatPhoto)
                if ($cat->photos) {
                    foreach ($cat->photos as $cp) {
                        if (empty($cp->photo_path)) continue;
                        $cpFps = $cp->spatial_fingerprint ?: CatBiometricService::getOrGenerateFingerprints($cp->photo_path);
                        if ($cpFps && empty($cp->spatial_fingerprint)) {
                            $cp->spatial_fingerprint = $cpFps;
                            $cp->color_fingerprint = $cpFps['color'];
                            $cp->saveQuietly();
                        }
                        if ($cpFps) {
                            $eval = CatBiometricService::evaluateMatchScore($queryBiometrics, [
                                'embedding' => $cp->photo_embedding,
                                'spatial'   => $cpFps['spatial'] ?? null,
                                'color'     => $cpFps['color'] ?? $cp->color_fingerprint,
                                'hash'      => $cpFps['hash'] ?? null,
                            ]);
                            if ($eval['final_score'] > $bestCatScore) {
                                $bestCatScore = $eval['final_score'];
                                $bestCatEval = $eval;
                                $bestPhotoUrl = asset('storage/' . $cp->photo_path);
                                $bestPhotoLabel = $cp->label ?: 'Galeri KTAM';
                            }
                        }
                    }
                }

                if ($bestCatEval && $bestCatScore >= $minThreshold) {
                    $candidates[] = [
                        'source_type'           => 'member_cat',
                        'source_label'          => 'Kucing Member KTAM',
                        'id'                    => $cat->id,
                        'id_kucing'             => $cat->name . ' (KTAM)',
                        'kampus'                => 'KTAM',
                        'display_kampus'        => 'Kucing Member (' . ($cat->owner ? $cat->owner->name : 'Member') . ')',
                        'zona'                  => 'Ras: ' . ($cat->breed ?: 'Domestik'),
                        'usia'                  => $cat->date_of_birth ? ($cat->date_of_birth->age . ' thn') : 'Dewasa',
                        'gender'                => ($cat->gender === 'male' ? 'Jantan' : ($cat->gender === 'female' ? 'Betina' : '-')),
                        'warna'                 => $cat->breed ?: 'Kucing Ras/Domestik',
                        'display_warna'         => $cat->breed ?: 'Kucing Ras/Domestik',
                        'bcs'                   => 'Terawat',
                        'foto_wajah_url'        => $bestPhotoUrl,
                        'matched_angle'         => $bestPhotoLabel,
                        'detail_url'            => route('cat.edit', $cat->id),
                        'created_at_formatted'  => $cat->created_at ? $cat->created_at->format('d M Y, H:i') : '-',
                        'similarity'            => round($bestCatScore, 4),
                        'similarity_percent'    => round($bestCatScore * 100, 1),
                        'metrics'               => [
                            'deep'    => $bestCatEval['deep_score'],
                            'spatial' => $bestCatEval['spatial_score'],
                            'color'   => $bestCatEval['color_score'],
                            'hash'    => $bestCatEval['hash_score'],
                        ],
                    ];
                }
            }

            // 3. Match with Stray Cat Surveys (stray_cat_surveys table)
            $surveys = StrayCatSurvey::whereNotNull('photo_path')->get();
            $totalCompared += $surveys->count();

            foreach ($surveys as $srv) {
                $srvFps = $srv->spatial_fingerprint ?: CatBiometricService::getOrGenerateFingerprints($srv->photo_path);
                if ($srvFps && empty($srv->spatial_fingerprint)) {
                    $srv->spatial_fingerprint = $srvFps;
                    $srv->color_fingerprint = $srvFps['color'];
                    $srv->saveQuietly();
                }

                if (!$srvFps) continue;

                $eval = CatBiometricService::evaluateMatchScore($queryBiometrics, [
                    'embedding' => $srv->photo_embedding,
                    'spatial'   => $srvFps['spatial'] ?? null,
                    'color'     => $srvFps['color'] ?? $srv->color_fingerprint,
                    'hash'      => $srvFps['hash'] ?? null,
                ]);

                if ($eval['final_score'] >= $minThreshold) {
                    $candidates[] = [
                        'source_type'           => 'survey',
                        'source_label'          => 'Surveilans Lapangan',
                        'id'                    => $srv->id,
                        'id_kucing'             => 'SRV-' . str_pad($srv->id, 3, '0', STR_PAD_LEFT),
                        'kampus'                => $srv->campus_location ?: 'PTMA',
                        'display_kampus'        => $srv->campus_location ?: 'PTMA',
                        'zona'                  => $srv->zone ?: 'Zona Survei',
                        'usia'                  => 'Liar',
                        'gender'                => '-',
                        'warna'                 => 'Surveilans',
                        'display_warna'         => 'Kucing Liar',
                        'bcs'                   => '-',
                        'foto_wajah_url'        => asset('storage/' . $srv->photo_path),
                        'matched_angle'         => 'Foto Dokumentasi Surveilans',
                        'detail_url'            => route('volunteer.surveillance.index'),
                        'created_at_formatted'  => $srv->created_at ? $srv->created_at->format('d M Y, H:i') : '-',
                        'similarity'            => round($eval['final_score'], 4),
                        'similarity_percent'    => round($eval['final_score'] * 100, 1),
                        'metrics'               => [
                            'deep'    => $eval['deep_score'],
                            'spatial' => $eval['spatial_score'],
                            'color'   => $eval['color_score'],
                            'hash'    => $eval['hash_score'],
                        ],
                    ];
                }
            }
        }

        // Sort descending by similarity
        usort($candidates, function ($a, $b) {
            return $b['similarity'] <=> $a['similarity'];
        });

        $topCandidates = array_slice($candidates, 0, 8);
        $bestMatch = !empty($topCandidates) ? $topCandidates[0] : null;
        $isLikelyMatch = $bestMatch && (
            $bestMatch['similarity_percent'] >= 68.0 ||
            (!empty($bestMatch['metrics']['deep']) && $bestMatch['metrics']['deep'] >= 72.0)
        );

        return response()->json([
            'success'        => true,
            'total_compared' => $totalCompared,
            'match_count'    => count($topCandidates),
            'is_likely_match'=> $isLikelyMatch,
            'best_match'     => $bestMatch,
            'matches'        => $topCandidates,
        ]);
    }

    /**
     * Get records across all cat tables that are missing visual deep embeddings.
     */
    public function getMissingEmbeddings(Request $request)
    {
        $records = [];

        // 1. PTMA Census missing embeddings (all 4 slots)
        $censuses = PtmaCatCensus::where(function ($q) {
            $q->where(function ($q2) {
                $q2->whereNotNull('foto_wajah')->whereNull('foto_wajah_embedding');
            })->orWhere(function ($q2) {
                $q2->whereNotNull('foto_atas')->whereNull('multi_embeddings');
            })->orWhere(function ($q2) {
                $q2->whereNotNull('foto_samping_kiri')->whereNull('multi_embeddings');
            });
        })->limit(15)->get();

        foreach ($censuses as $c) {
            $slots = [
                'wajah'   => $c->foto_wajah_url,
                'atas'    => $c->foto_atas_url,
                'samping' => $c->foto_samping_kiri_url,
                'opsional'=> $c->foto_opsional_url,
            ];
            $multi = is_array($c->multi_embeddings) ? $c->multi_embeddings : [];

            foreach ($slots as $slotKey => $url) {
                if ($url && ($slotKey === 'wajah' ? empty($c->foto_wajah_embedding) : empty($multi[$slotKey]))) {
                    $records[] = [
                        'type'     => 'census',
                        'id'       => $c->id,
                        'id_label' => $c->id_kucing . ' (' . ucfirst($slotKey) . ')',
                        'slot'     => $slotKey,
                        'photo_url'=> $url,
                    ];
                }
            }
        }

        // 2. Member cats
        if (count($records) < 15) {
            $cats = Cat::whereNotNull('photo_path')->whereNull('photo_embedding')->limit(10)->get();
            foreach ($cats as $cat) {
                $records[] = [
                    'type'     => 'cat',
                    'id'       => $cat->id,
                    'id_label' => $cat->name . ' (KTAM)',
                    'slot'     => 'primary',
                    'photo_url'=> $cat->primary_photo_url,
                ];
            }
        }

        // 3. Surveys
        if (count($records) < 15) {
            $surveys = StrayCatSurvey::whereNotNull('photo_path')->whereNull('photo_embedding')->limit(10)->get();
            foreach ($surveys as $srv) {
                $records[] = [
                    'type'     => 'survey',
                    'id'       => $srv->id,
                    'id_label' => 'Surveilans #' . $srv->id,
                    'slot'     => 'primary',
                    'photo_url'=> asset('storage/' . $srv->photo_path),
                ];
            }
        }

        return response()->json([
            'success'         => true,
            'records'         => array_slice($records, 0, 15),
            'remaining_count' => count($records),
        ]);
    }

    /**
     * Sync computed embeddings from client across modules.
     */
    public function syncEmbeddings(Request $request)
    {
        $request->validate([
            'items'             => 'required|array',
            'items.*.id'        => 'required|integer',
            'items.*.embedding' => 'required|array',
            'items.*.type'      => 'nullable|string',
            'items.*.slot'      => 'nullable|string',
        ]);

        $updated = 0;
        foreach ($request->items as $item) {
            $type = $item['type'] ?? 'census';
            $slot = $item['slot'] ?? 'wajah';
            $emb = $item['embedding'];

            if ($type === 'census') {
                $census = PtmaCatCensus::find($item['id']);
                if ($census) {
                    $multi = is_array($census->multi_embeddings) ? $census->multi_embeddings : [];
                    $multi[$slot] = $emb;
                    $census->multi_embeddings = $multi;
                    if ($slot === 'wajah') {
                        $census->foto_wajah_embedding = $emb;
                    }
                    $census->saveQuietly();
                    $updated++;
                }
            } elseif ($type === 'cat') {
                $cat = Cat::find($item['id']);
                if ($cat) {
                    $cat->photo_embedding = $emb;
                    $cat->saveQuietly();
                    $updated++;
                }
            } elseif ($type === 'survey') {
                $srv = StrayCatSurvey::find($item['id']);
                if ($srv) {
                    $srv->photo_embedding = $emb;
                    $srv->saveQuietly();
                    $updated++;
                }
            }
        }

        return response()->json([
            'success'       => true,
            'updated_count' => $updated,
        ]);
    }

    /**
     * Show the form for creating a new census record.
     */
    public function create()
    {
        $defaultZones = [
            'UMY - Selatan',
            'UMY - Utara',
            'UMY - Tengah (admisi, AR, maskam, boga)',
            'Unires & E8',
        ];
        $dbZones = PtmaCatCensus::whereNotNull('zona')->pluck('zona')->toArray();
        $zones = array_values(array_unique(array_filter(array_merge($defaultZones, $dbZones))));

        return view('volunteer.census.create', compact('zones'));
    }

    /**
     * API endpoint to get the next sequential ID for a selected campus.
     */
    public function nextId(Request $request)
    {
        $kampus = $request->query('kampus', 'UMY');
        $kampusCustom = $request->query('kampus_custom');

        $result = PtmaCatCensus::generateNextId($kampus, $kampusCustom);

        return response()->json([
            'success'         => true,
            'id_kucing'       => $result['id_kucing'],
            'sequence_number' => $result['sequence_number'],
            'prefix'          => $result['prefix'],
        ]);
    }

    /**
     * Store a newly created census record in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'id_kucing'             => 'required|string|max:50|unique:ptma_cat_censuses,id_kucing',
            'kampus'                => 'required|string|max:50',
            'kampus_custom'         => 'nullable|string|max:100|required_if:kampus,Lainnya',
            'zona'                  => 'required|string|max:255',
            'latitude'              => 'nullable|numeric|between:-90,90',
            'longitude'             => 'nullable|numeric|between:-180,180',
            'usia'                  => 'required|string|max:50',
            'gender'                => 'required|string|max:50',
            'warna'                 => 'required|string|max:50',
            'warna_custom'          => 'nullable|string|max:100|required_if:warna,Lainnya',
            'bcs'                   => 'required|string|max:50',
            'kondisi_klinis'        => 'nullable|array',
            'kondisi_klinis.*'      => 'string|max:100',
            'kondisi_klinis_custom' => 'nullable|string|max:150',
            'panjang_badan_cm'      => 'nullable|numeric|min:0|max:200',
            'panjang_ekor_cm'       => 'nullable|numeric|min:0|max:100',
            'jarak_pakan'           => 'nullable|integer|min:0|max:5000',
            'jenis_pakan'           => 'required|string|max:150',
            'jenis_pakan_custom'    => 'nullable|string|max:150|required_if:jenis_pakan,Lainnya',
            'ancaman'               => 'required|string|max:150',
            'ancaman_custom'        => 'nullable|string|max:150|required_if:ancaman,Lainnya',
            'catatan'               => 'nullable|string|max:2000',

            // Photos validation
            'foto_wajah'            => 'nullable|image|mimes:jpeg,png,jpg,webp|max:8192',
            'foto_atas'             => 'nullable|image|mimes:jpeg,png,jpg,webp|max:8192',
            'foto_samping_kiri'     => 'nullable|image|mimes:jpeg,png,jpg,webp|max:8192',
            'foto_opsional'         => 'nullable|image|mimes:jpeg,png,jpg,webp|max:8192',
            'foto_wajah_cam'        => 'nullable|string',
            'foto_atas_cam'         => 'nullable|string',
            'foto_samping_kiri_cam' => 'nullable|string',
            'foto_opsional_cam'     => 'nullable|string',
            'foto_wajah_embedding'  => 'nullable',
        ]);

        // Process photos & biometrics
        $multiSpatial = [];

        $binaryWajah = $this->getPhotoBinary($request, 'foto_wajah', 'foto_wajah_cam');
        $fotoWajah = $binaryWajah ? $this->compressAndSaveImage($binaryWajah) : null;
        $colorFingerprint = $binaryWajah ? CatBiometricService::extractColorFingerprint($binaryWajah) : null;
        if ($binaryWajah) {
            $multiSpatial['wajah'] = [
                'spatial' => CatBiometricService::extractSpatialFingerprint($binaryWajah),
                'color'   => $colorFingerprint,
                'hash'    => CatBiometricService::extractDHash($binaryWajah),
            ];
        }

        $binaryAtas = $this->getPhotoBinary($request, 'foto_atas', 'foto_atas_cam');
        $fotoAtas = $binaryAtas ? $this->compressAndSaveImage($binaryAtas) : null;
        if ($binaryAtas) {
            $multiSpatial['atas'] = [
                'spatial' => CatBiometricService::extractSpatialFingerprint($binaryAtas),
                'color'   => CatBiometricService::extractColorFingerprint($binaryAtas),
                'hash'    => CatBiometricService::extractDHash($binaryAtas),
            ];
        }

        $binarySamping = $this->getPhotoBinary($request, 'foto_samping_kiri', 'foto_samping_kiri_cam');
        $fotoSampingKiri = $binarySamping ? $this->compressAndSaveImage($binarySamping) : null;
        if ($binarySamping) {
            $multiSpatial['samping'] = [
                'spatial' => CatBiometricService::extractSpatialFingerprint($binarySamping),
                'color'   => CatBiometricService::extractColorFingerprint($binarySamping),
                'hash'    => CatBiometricService::extractDHash($binarySamping),
            ];
        }

        $binaryOpsional = $this->getPhotoBinary($request, 'foto_opsional', 'foto_opsional_cam');
        $fotoOpsional = $binaryOpsional ? $this->compressAndSaveImage($binaryOpsional) : null;
        if ($binaryOpsional) {
            $multiSpatial['opsional'] = [
                'spatial' => CatBiometricService::extractSpatialFingerprint($binaryOpsional),
                'color'   => CatBiometricService::extractColorFingerprint($binaryOpsional),
                'hash'    => CatBiometricService::extractDHash($binaryOpsional),
            ];
        }

        // Embedding from client if provided
        $embeddingWajah = null;
        if ($request->filled('foto_wajah_embedding')) {
            $emb = $request->input('foto_wajah_embedding');
            $embeddingWajah = is_string($emb) ? json_decode($emb, true) : $emb;
        }

        // Extract sequence number from ID (e.g. UMY-00000001 -> 1)
        $idKucing = trim($request->id_kucing);
        $sequenceNumber = 1;
        if (preg_match('/-(\d+)$/', $idKucing, $matches)) {
            $sequenceNumber = (int) $matches[1];
        }

        $kondisiKlinis = $request->kondisi_klinis ?? [];
        if ($request->filled('kondisi_klinis_custom')) {
            $kondisiKlinis[] = trim($request->kondisi_klinis_custom);
        }
        if (empty($kondisiKlinis)) {
            $kondisiKlinis = ['Sehat'];
        }

        $census = PtmaCatCensus::create([
            'volunteer_id'         => Auth::id(),
            'id_kucing'            => $idKucing,
            'sequence_number'      => $sequenceNumber,
            'kampus'               => $request->kampus,
            'kampus_custom'        => $request->kampus === 'Lainnya' ? $request->kampus_custom : null,
            'zona'                 => $request->zona,
            'latitude'             => $request->latitude,
            'longitude'            => $request->longitude,
            'usia'                 => $request->usia,
            'gender'               => $request->gender,
            'warna'                => $request->warna,
            'warna_custom'         => $request->warna === 'Lainnya' ? $request->warna_custom : null,
            'foto_wajah'           => $fotoWajah,
            'foto_atas'            => $fotoAtas,
            'foto_samping_kiri'    => $fotoSampingKiri,
            'foto_opsional'        => $fotoOpsional,
            'foto_wajah_embedding' => $embeddingWajah,
            'color_fingerprint'    => $colorFingerprint,
            'spatial_fingerprint'  => $multiSpatial,
            'bcs'                  => $request->bcs,
            'kondisi_klinis'       => array_values(array_unique($kondisiKlinis)),
            'panjang_badan_cm'     => $request->panjang_badan_cm,
            'panjang_ekor_cm'      => $request->panjang_ekor_cm,
            'jarak_pakan'          => $request->jarak_pakan,
            'jenis_pakan'          => $request->jenis_pakan,
            'jenis_pakan_custom'   => $request->jenis_pakan === 'Lainnya' ? $request->jenis_pakan_custom : null,
            'ancaman'              => $request->ancaman,
            'ancaman_custom'       => $request->ancaman === 'Lainnya' ? $request->ancaman_custom : null,
            'catatan'              => $request->catatan,
        ]);

        return redirect()->route('volunteer.census.index')
            ->with('success', "Data Sensus Kucing [{$census->id_kucing}] berhasil disimpan!");
    }

    /**
     * Display the specified census record.
     */
    public function show(PtmaCatCensus $census)
    {
        $census->load('volunteer');
        return view('volunteer.census.show', ['census' => $census]);
    }

    /**
     * Show the form for editing the specified census record.
     */
    public function edit(PtmaCatCensus $census)
    {
        $defaultZones = [
            'UMY - Selatan',
            'UMY - Utara',
            'UMY - Tengah (admisi, AR, maskam, boga)',
            'Unires & E8',
        ];
        $dbZones = PtmaCatCensus::whereNotNull('zona')->pluck('zona')->toArray();
        $zones = array_values(array_unique(array_filter(array_merge($defaultZones, $dbZones))));

        return view('volunteer.census.edit', [
            'census' => $census,
            'zones'  => $zones,
        ]);
    }

    /**
     * Update the specified census record in storage.
     */
    public function update(Request $request, PtmaCatCensus $census)
    {
        $request->validate([
            'id_kucing'             => ['required', 'string', 'max:50', Rule::unique('ptma_cat_censuses', 'id_kucing')->ignore($census->id)],
            'kampus'                => 'required|string|max:50',
            'kampus_custom'         => 'nullable|string|max:100|required_if:kampus,Lainnya',
            'zona'                  => 'required|string|max:255',
            'latitude'              => 'nullable|numeric|between:-90,90',
            'longitude'             => 'nullable|numeric|between:-180,180',
            'usia'                  => 'required|string|max:50',
            'gender'                => 'required|string|max:50',
            'warna'                 => 'required|string|max:50',
            'warna_custom'          => 'nullable|string|max:100|required_if:warna,Lainnya',
            'bcs'                   => 'required|string|max:50',
            'kondisi_klinis'        => 'nullable|array',
            'kondisi_klinis.*'      => 'string|max:100',
            'kondisi_klinis_custom' => 'nullable|string|max:150',
            'panjang_badan_cm'      => 'nullable|numeric|min:0|max:200',
            'panjang_ekor_cm'       => 'nullable|numeric|min:0|max:100',
            'jarak_pakan'           => 'nullable|integer|min:0|max:5000',
            'jenis_pakan'           => 'required|string|max:150',
            'jenis_pakan_custom'    => 'nullable|string|max:150|required_if:jenis_pakan,Lainnya',
            'ancaman'               => 'required|string|max:150',
            'ancaman_custom'        => 'nullable|string|max:150|required_if:ancaman,Lainnya',
            'catatan'               => 'nullable|string|max:2000',

            // Photos validation
            'foto_wajah'            => 'nullable|image|mimes:jpeg,png,jpg,webp|max:8192',
            'foto_atas'             => 'nullable|image|mimes:jpeg,png,jpg,webp|max:8192',
            'foto_samping_kiri'     => 'nullable|image|mimes:jpeg,png,jpg,webp|max:8192',
            'foto_opsional'         => 'nullable|image|mimes:jpeg,png,jpg,webp|max:8192',
            'foto_wajah_cam'        => 'nullable|string',
            'foto_atas_cam'         => 'nullable|string',
            'foto_samping_kiri_cam' => 'nullable|string',
            'foto_opsional_cam'     => 'nullable|string',
            'foto_wajah_embedding'  => 'nullable',
        ]);

        $kondisiKlinis = $request->kondisi_klinis ?? [];
        if ($request->filled('kondisi_klinis_custom')) {
            $kondisiKlinis[] = trim($request->kondisi_klinis_custom);
        }
        if (empty($kondisiKlinis)) {
            $kondisiKlinis = ['Sehat'];
        }

        $data = [
            'id_kucing'           => trim($request->id_kucing),
            'kampus'              => $request->kampus,
            'kampus_custom'       => $request->kampus === 'Lainnya' ? $request->kampus_custom : null,
            'zona'                => $request->zona,
            'latitude'            => $request->latitude,
            'longitude'           => $request->longitude,
            'usia'                => $request->usia,
            'gender'              => $request->gender,
            'warna'               => $request->warna,
            'warna_custom'        => $request->warna === 'Lainnya' ? $request->warna_custom : null,
            'bcs'                 => $request->bcs,
            'kondisi_klinis'      => array_values(array_unique($kondisiKlinis)),
            'panjang_badan_cm'    => $request->panjang_badan_cm,
            'panjang_ekor_cm'     => $request->panjang_ekor_cm,
            'jarak_pakan'         => $request->jarak_pakan,
            'jenis_pakan'         => $request->jenis_pakan,
            'jenis_pakan_custom'  => $request->jenis_pakan === 'Lainnya' ? $request->jenis_pakan_custom : null,
            'ancaman'             => $request->ancaman,
            'ancaman_custom'      => $request->ancaman === 'Lainnya' ? $request->ancaman_custom : null,
            'catatan'             => $request->catatan,
        ];

        // Process updated photos if provided
        $spatialFp = is_array($census->spatial_fingerprint) ? $census->spatial_fingerprint : [];

        $binaryWajah = $this->getPhotoBinary($request, 'foto_wajah', 'foto_wajah_cam');
        if ($binaryWajah) {
            if ($census->foto_wajah) Storage::disk('public')->delete($census->foto_wajah);
            $data['foto_wajah'] = $this->compressAndSaveImage($binaryWajah);
            $data['color_fingerprint'] = CatBiometricService::extractColorFingerprint($binaryWajah);
            $spatialFp['wajah'] = [
                'spatial' => CatBiometricService::extractSpatialFingerprint($binaryWajah),
                'color'   => $data['color_fingerprint'],
                'hash'    => CatBiometricService::extractDHash($binaryWajah),
            ];
        }

        if ($request->filled('foto_wajah_embedding')) {
            $emb = $request->input('foto_wajah_embedding');
            $data['foto_wajah_embedding'] = is_string($emb) ? json_decode($emb, true) : $emb;
        }

        $binaryAtas = $this->getPhotoBinary($request, 'foto_atas', 'foto_atas_cam');
        if ($binaryAtas) {
            if ($census->foto_atas) Storage::disk('public')->delete($census->foto_atas);
            $data['foto_atas'] = $this->compressAndSaveImage($binaryAtas);
            $spatialFp['atas'] = [
                'spatial' => CatBiometricService::extractSpatialFingerprint($binaryAtas),
                'color'   => CatBiometricService::extractColorFingerprint($binaryAtas),
                'hash'    => CatBiometricService::extractDHash($binaryAtas),
            ];
        }

        $binarySamping = $this->getPhotoBinary($request, 'foto_samping_kiri', 'foto_samping_kiri_cam');
        if ($binarySamping) {
            if ($census->foto_samping_kiri) Storage::disk('public')->delete($census->foto_samping_kiri);
            $data['foto_samping_kiri'] = $this->compressAndSaveImage($binarySamping);
            $spatialFp['samping'] = [
                'spatial' => CatBiometricService::extractSpatialFingerprint($binarySamping),
                'color'   => CatBiometricService::extractColorFingerprint($binarySamping),
                'hash'    => CatBiometricService::extractDHash($binarySamping),
            ];
        }

        $binaryOpsional = $this->getPhotoBinary($request, 'foto_opsional', 'foto_opsional_cam');
        if ($binaryOpsional) {
            if ($census->foto_opsional) Storage::disk('public')->delete($census->foto_opsional);
            $data['foto_opsional'] = $this->compressAndSaveImage($binaryOpsional);
            $spatialFp['opsional'] = [
                'spatial' => CatBiometricService::extractSpatialFingerprint($binaryOpsional),
                'color'   => CatBiometricService::extractColorFingerprint($binaryOpsional),
                'hash'    => CatBiometricService::extractDHash($binaryOpsional),
            ];
        }

        $data['spatial_fingerprint'] = $spatialFp;

        $census->update($data);

        return redirect()->route('volunteer.census.index')
            ->with('success', "Data Sensus Kucing [{$census->id_kucing}] berhasil diperbarui!");
    }

    /**
     * Remove the specified census record from storage.
     */
    public function destroy(PtmaCatCensus $census)
    {
        // Remove photos
        foreach ([$census->foto_wajah, $census->foto_atas, $census->foto_samping_kiri, $census->foto_opsional] as $photo) {
            if ($photo) {
                Storage::disk('public')->delete($photo);
            }
        }

        $idKucing = $census->id_kucing;
        $census->delete();

        return redirect()->route('volunteer.census.index')
            ->with('success', "Data Sensus Kucing [{$idKucing}] berhasil dihapus.");
    }

    /**
     * Export all census records to CSV.
     */
    public function exportCsv()
    {
        $censuses = PtmaCatCensus::with('volunteer')->latest()->get();

        $filename = 'sensus_kucing_ptma_' . date('Ymd_His') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0',
        ];

        $callback = function () use ($censuses) {
            $output = fopen('php://output', 'w');
            fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($output, [
                'ID Kucing',
                'Kampus',
                'Zona',
                'Latitude',
                'Longitude',
                'Usia',
                'Gender',
                'Warna Bulu',
                'Body Condition Score (BCS)',
                'Kondisi Klinis / Lesi',
                'Panjang Badan (cm)',
                'Panjang Ekor (cm)',
                'Jarak Sumber Pakan (m)',
                'Jenis Pakan Dominan',
                'Ancaman Terbesar',
                'Catatan Lapangan',
                'Petugas Sensus (Relawan)',
                'Tanggal Input',
                'Foto Wajah URL',
                'Foto Atas URL',
                'Foto Samping Kiri URL',
            ]);

            foreach ($censuses as $c) {
                fputcsv($output, [
                    $c->id_kucing,
                    $c->display_kampus,
                    $c->zona,
                    $c->latitude,
                    $c->longitude,
                    $c->usia,
                    $c->gender,
                    $c->display_warna,
                    $c->bcs,
                    is_array($c->kondisi_klinis) ? implode(', ', $c->kondisi_klinis) : $c->kondisi_klinis,
                    $c->panjang_badan_cm,
                    $c->panjang_ekor_cm,
                    $c->jarak_pakan,
                    $c->display_jenis_pakan,
                    $c->display_ancaman,
                    $c->catatan,
                    $c->volunteer ? $c->volunteer->name : '-',
                    $c->created_at ? $c->created_at->format('Y-m-d H:i:s') : '-',
                    $c->foto_wajah_url ? url($c->foto_wajah_url) : '',
                    $c->foto_atas_url ? url($c->foto_atas_url) : '',
                    $c->foto_samping_kiri_url ? url($c->foto_samping_kiri_url) : '',
                ]);
            }

            fclose($output);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Helper to get raw photo binary from file or camera base64.
     */
    private function getPhotoBinary(Request $request, string $fileKey, string $camKey): ?string
    {
        if ($request->hasFile($fileKey)) {
            $file = $request->file($fileKey);
            return file_get_contents($file->getPathname());
        }

        if ($request->filled($camKey)) {
            $base64Data = $request->input($camKey);
            if (preg_match('/^data:image\/(\w+);base64,/', $base64Data, $type)) {
                $base64Data = substr($base64Data, strpos($base64Data, ',') + 1);
                $decoded = base64_decode($base64Data);
                if ($decoded) {
                    return $decoded;
                }
            }
        }

        return null;
    }

    /**
     * Helper to process image file upload or camera base64 data.
     */
    private function handlePhotoUpload(Request $request, string $fileKey, string $camKey): ?string
    {
        $binary = $this->getPhotoBinary($request, $fileKey, $camKey);
        if ($binary) {
            return $this->compressAndSaveImage($binary);
        }
        return null;
    }

    /**
     * Resize and save image to storage under 1000px width.
     */
    private function compressAndSaveImage(string $binaryData): string
    {
        $sourceImage = @imagecreatefromstring($binaryData);
        $filename = 'ptma-census/' . uniqid('census_', true) . '.jpg';
        $fullPath = storage_path('app/public/' . $filename);

        if (!file_exists(dirname($fullPath))) {
            mkdir(dirname($fullPath), 0755, true);
        }

        if (!$sourceImage) {
            // Fallback raw save if GD fails
            file_put_contents($fullPath, $binaryData);
            return $filename;
        }

        $origWidth = imagesx($sourceImage);
        $origHeight = imagesy($sourceImage);
        $maxDim = 1000;

        if ($origWidth > $maxDim || $origHeight > $maxDim) {
            $ratio = min($maxDim / $origWidth, $maxDim / $origHeight);
            $newWidth = (int) round($origWidth * $ratio);
            $newHeight = (int) round($origHeight * $ratio);
        } else {
            $newWidth = $origWidth;
            $newHeight = $origHeight;
        }

        $resized = imagecreatetruecolor($newWidth, $newHeight);
        $white = imagecolorallocate($resized, 255, 255, 255);
        imagefill($resized, 0, 0, $white);

        imagecopyresampled($resized, $sourceImage, 0, 0, 0, 0, $newWidth, $newHeight, $origWidth, $origHeight);
        imagejpeg($resized, $fullPath, 75);

        imagedestroy($sourceImage);
        imagedestroy($resized);

        return $filename;
    }
}
