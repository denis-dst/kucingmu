<?php

namespace App\Http\Controllers;

use App\Models\PtmaCatCensus;
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

        $stats = [
            'total' => PtmaCatCensus::count(),
            'umy' => PtmaCatCensus::where('kampus', 'UMY')->count(),
            'uad' => PtmaCatCensus::where('kampus', 'UAD')->count(),
            'ump' => PtmaCatCensus::where('kampus', 'UMP')->count(),
            'ums' => PtmaCatCensus::where('kampus', 'UMS')->count(),
            'my_submissions' => PtmaCatCensus::where('volunteer_id', Auth::id())->count(),
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

        $totalRegistered = PtmaCatCensus::count();
        $campuses = ['UMY', 'UAD', 'UMP', 'UMS'];

        return view('volunteer.census.scan', compact('zones', 'totalRegistered', 'campuses'));
    }

    /**
     * Match a scanned cat embedding / photo against master records in database.
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

        $queryColorFingerprint = null;
        if ($rawBinary) {
            $queryColorFingerprint = PtmaCatCensus::extractColorFingerprint($rawBinary);
        }

        if (empty($queryEmbedding) && empty($queryColorFingerprint)) {
            return response()->json([
                'success' => false,
                'message' => 'Foto atau data embedding visual wajib disertakan untuk pemindaian.',
            ], 422);
        }

        $query = PtmaCatCensus::query();
        if ($request->filled('kampus') && $request->kampus !== 'Semua') {
            $query->where('kampus', $request->kampus);
        }

        $censuses = $query->where(function ($q) {
            $q->whereNotNull('foto_wajah')
              ->orWhereNotNull('foto_wajah_embedding')
              ->orWhereNotNull('color_fingerprint');
        })->get();

        $minThreshold = (float) $request->input('threshold', 0.45);
        $candidates = [];

        foreach ($censuses as $census) {
            $embeddingSim = null;
            $colorSim = null;

            if (!empty($queryEmbedding) && !empty($census->foto_wajah_embedding)) {
                $embeddingSim = PtmaCatCensus::cosineSimilarity($queryEmbedding, $census->foto_wajah_embedding);
            }

            if (!empty($queryColorFingerprint) && !empty($census->color_fingerprint)) {
                $colorSim = PtmaCatCensus::cosineSimilarity($queryColorFingerprint, $census->color_fingerprint);
            }

            if ($embeddingSim === null && $colorSim === null) {
                continue;
            }

            // Calculate hybrid visual score (75% deep features, 25% color distribution)
            if ($embeddingSim !== null && $colorSim !== null) {
                $finalScore = ($embeddingSim * 0.75) + ($colorSim * 0.25);
            } elseif ($embeddingSim !== null) {
                $finalScore = $embeddingSim;
            } else {
                $finalScore = $colorSim;
            }

            // Slight boost if color category matches
            if ($request->filled('warna') && $request->warna !== 'Lainnya' && $census->warna === $request->warna) {
                $finalScore = min(1.0, $finalScore + 0.04);
            }

            if ($finalScore >= $minThreshold) {
                $candidates[] = [
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
                    'foto_wajah_url'        => $census->foto_wajah_url,
                    'foto_atas_url'         => $census->foto_atas_url,
                    'foto_samping_kiri_url' => $census->foto_samping_kiri_url,
                    'created_at_formatted'  => $census->created_at ? $census->created_at->format('d M Y, H:i') : '-',
                    'similarity'            => round($finalScore, 4),
                    'similarity_percent'    => round($finalScore * 100, 1),
                ];
            }
        }

        // Sort descending by similarity
        usort($candidates, function ($a, $b) {
            return $b['similarity'] <=> $a['similarity'];
        });

        $topCandidates = array_slice($candidates, 0, 6);
        $bestMatch = !empty($topCandidates) ? $topCandidates[0] : null;
        $isLikelyMatch = $bestMatch && ($bestMatch['similarity_percent'] >= 72.0);

        return response()->json([
            'success'        => true,
            'total_compared' => $censuses->count(),
            'match_count'    => count($topCandidates),
            'is_likely_match'=> $isLikelyMatch,
            'best_match'     => $bestMatch,
            'matches'        => $topCandidates,
        ]);
    }

    /**
     * Get records with photos that are missing visual embeddings.
     */
    public function getMissingEmbeddings(Request $request)
    {
        $records = PtmaCatCensus::whereNotNull('foto_wajah')
            ->whereNull('foto_wajah_embedding')
            ->select(['id', 'id_kucing', 'foto_wajah'])
            ->limit(20)
            ->get()
            ->map(function ($c) {
                return [
                    'id'             => $c->id,
                    'id_kucing'      => $c->id_kucing,
                    'foto_wajah_url' => $c->foto_wajah_url,
                ];
            });

        return response()->json([
            'success'         => true,
            'records'         => $records,
            'remaining_count' => PtmaCatCensus::whereNotNull('foto_wajah')->whereNull('foto_wajah_embedding')->count(),
        ]);
    }

    /**
     * Sync computed embeddings from client.
     */
    public function syncEmbeddings(Request $request)
    {
        $request->validate([
            'items'             => 'required|array',
            'items.*.id'        => 'required|integer|exists:ptma_cat_censuses,id',
            'items.*.embedding' => 'required|array',
        ]);

        $updated = 0;
        foreach ($request->items as $item) {
            $census = PtmaCatCensus::find($item['id']);
            if ($census) {
                $census->foto_wajah_embedding = $item['embedding'];
                $census->save();
                $updated++;
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

        // Process photos & fingerprints
        $binaryWajah = $this->getPhotoBinary($request, 'foto_wajah', 'foto_wajah_cam');
        $fotoWajah = $binaryWajah ? $this->compressAndSaveImage($binaryWajah) : null;
        $colorFingerprint = $binaryWajah ? PtmaCatCensus::extractColorFingerprint($binaryWajah) : null;

        $binaryAtas = $this->getPhotoBinary($request, 'foto_atas', 'foto_atas_cam');
        $fotoAtas = $binaryAtas ? $this->compressAndSaveImage($binaryAtas) : null;

        $binarySamping = $this->getPhotoBinary($request, 'foto_samping_kiri', 'foto_samping_kiri_cam');
        $fotoSampingKiri = $binarySamping ? $this->compressAndSaveImage($binarySamping) : null;

        $binaryOpsional = $this->getPhotoBinary($request, 'foto_opsional', 'foto_opsional_cam');
        $fotoOpsional = $binaryOpsional ? $this->compressAndSaveImage($binaryOpsional) : null;

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
        $binaryWajah = $this->getPhotoBinary($request, 'foto_wajah', 'foto_wajah_cam');
        if ($binaryWajah) {
            if ($census->foto_wajah) Storage::disk('public')->delete($census->foto_wajah);
            $data['foto_wajah'] = $this->compressAndSaveImage($binaryWajah);
            $data['color_fingerprint'] = PtmaCatCensus::extractColorFingerprint($binaryWajah);
        }

        if ($request->filled('foto_wajah_embedding')) {
            $emb = $request->input('foto_wajah_embedding');
            $data['foto_wajah_embedding'] = is_string($emb) ? json_decode($emb, true) : $emb;
        }

        $binaryAtas = $this->getPhotoBinary($request, 'foto_atas', 'foto_atas_cam');
        if ($binaryAtas) {
            if ($census->foto_atas) Storage::disk('public')->delete($census->foto_atas);
            $data['foto_atas'] = $this->compressAndSaveImage($binaryAtas);
        }

        $binarySamping = $this->getPhotoBinary($request, 'foto_samping_kiri', 'foto_samping_kiri_cam');
        if ($binarySamping) {
            if ($census->foto_samping_kiri) Storage::disk('public')->delete($census->foto_samping_kiri);
            $data['foto_samping_kiri'] = $this->compressAndSaveImage($binarySamping);
        }

        $binaryOpsional = $this->getPhotoBinary($request, 'foto_opsional', 'foto_opsional_cam');
        if ($binaryOpsional) {
            if ($census->foto_opsional) Storage::disk('public')->delete($census->foto_opsional);
            $data['foto_opsional'] = $this->compressAndSaveImage($binaryOpsional);
        }

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
