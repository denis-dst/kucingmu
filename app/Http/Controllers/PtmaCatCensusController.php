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
            'success' => true,
            'id_kucing' => $result['id_kucing'],
            'sequence_number' => $result['sequence_number'],
            'prefix' => $result['prefix'],
        ]);
    }

    /**
     * Store a newly created census record in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'id_kucing'           => 'required|string|max:50|unique:ptma_cat_censuses,id_kucing',
            'kampus'              => 'required|string|max:50',
            'kampus_custom'       => 'nullable|string|max:100|required_if:kampus,Lainnya',
            'zona'                => 'required|string|max:255',
            'latitude'            => 'nullable|numeric|between:-90,90',
            'longitude'           => 'nullable|numeric|between:-180,180',
            'usia'                => 'required|string|max:50',
            'gender'              => 'required|string|max:50',
            'warna'               => 'required|string|max:50',
            'warna_custom'        => 'nullable|string|max:100|required_if:warna,Lainnya',
            'bcs'                 => 'required|string|max:50',
            'kondisi_klinis'      => 'nullable|array',
            'kondisi_klinis.*'    => 'string|max:50',
            'panjang_badan_cm'    => 'nullable|numeric|min:0|max:200',
            'panjang_ekor_cm'     => 'nullable|numeric|min:0|max:100',
            'jarak_pakan'         => 'nullable|integer|min:0|max:5000',
            'jenis_pakan'         => 'required|string|max:100',
            'jenis_pakan_custom'  => 'nullable|string|max:150|required_if:jenis_pakan,Lainnya',
            'ancaman'             => 'required|string|max:100',
            'ancaman_custom'      => 'nullable|string|max:150|required_if:ancaman,Lainnya',
            'catatan'             => 'nullable|string|max:2000',

            // Photos validation
            'foto_wajah'          => 'nullable|image|mimes:jpeg,png,jpg,webp|max:8192',
            'foto_atas'           => 'nullable|image|mimes:jpeg,png,jpg,webp|max:8192',
            'foto_samping_kiri'   => 'nullable|image|mimes:jpeg,png,jpg,webp|max:8192',
            'foto_opsional'       => 'nullable|image|mimes:jpeg,png,jpg,webp|max:8192',
            'foto_wajah_cam'      => 'nullable|string',
            'foto_atas_cam'       => 'nullable|string',
            'foto_samping_kiri_cam'=> 'nullable|string',
            'foto_opsional_cam'   => 'nullable|string',
        ]);

        // Process photos (handling both file upload and live camera base64)
        $fotoWajah = $this->handlePhotoUpload($request, 'foto_wajah', 'foto_wajah_cam');
        $fotoAtas = $this->handlePhotoUpload($request, 'foto_atas', 'foto_atas_cam');
        $fotoSampingKiri = $this->handlePhotoUpload($request, 'foto_samping_kiri', 'foto_samping_kiri_cam');
        $fotoOpsional = $this->handlePhotoUpload($request, 'foto_opsional', 'foto_opsional_cam');

        // Extract sequence number from ID (e.g. UMY-00000001 -> 1)
        $idKucing = trim($request->id_kucing);
        $sequenceNumber = 1;
        if (preg_match('/-(\d+)$/', $idKucing, $matches)) {
            $sequenceNumber = (int) $matches[1];
        }

        $census = PtmaCatCensus::create([
            'volunteer_id'        => Auth::id(),
            'id_kucing'           => $idKucing,
            'sequence_number'     => $sequenceNumber,
            'kampus'              => $request->kampus,
            'kampus_custom'       => $request->kampus === 'Lainnya' ? $request->kampus_custom : null,
            'zona'                => $request->zona,
            'latitude'            => $request->latitude,
            'longitude'           => $request->longitude,
            'usia'                => $request->usia,
            'gender'              => $request->gender,
            'warna'               => $request->warna,
            'warna_custom'        => $request->warna === 'Lainnya' ? $request->warna_custom : null,
            'foto_wajah'          => $fotoWajah,
            'foto_atas'           => $fotoAtas,
            'foto_samping_kiri'   => $fotoSampingKiri,
            'foto_opsional'       => $fotoOpsional,
            'bcs'                 => $request->bcs,
            'kondisi_klinis'      => $request->kondisi_klinis ?? ['Sehat'],
            'panjang_badan_cm'    => $request->panjang_badan_cm,
            'panjang_ekor_cm'     => $request->panjang_ekor_cm,
            'jarak_pakan'         => $request->jarak_pakan,
            'jenis_pakan'         => $request->jenis_pakan,
            'jenis_pakan_custom'  => $request->jenis_pakan === 'Lainnya' ? $request->jenis_pakan_custom : null,
            'ancaman'             => $request->ancaman,
            'ancaman_custom'      => $request->ancaman === 'Lainnya' ? $request->ancaman_custom : null,
            'catatan'             => $request->catatan,
        ]);

        return redirect()->route('volunteer.census.index')
            ->with('success', "Data Sensus Kucing [{$census->id_kucing}] berhasil disimpan!");
    }

    /**
     * Display the specified census record.
     */
    public function show(PtmaCatCensus $sensu)
    {
        $sensu->load('volunteer');
        return view('volunteer.census.show', ['census' => $sensu]);
    }

    /**
     * Show the form for editing the specified census record.
     */
    public function edit(PtmaCatCensus $sensu)
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
            'census' => $sensu,
            'zones' => $zones
        ]);
    }

    /**
     * Update the specified census record in storage.
     */
    public function update(Request $request, PtmaCatCensus $sensu)
    {
        $request->validate([
            'id_kucing'           => ['required', 'string', 'max:50', Rule::unique('ptma_cat_censuses', 'id_kucing')->ignore($sensu->id)],
            'kampus'              => 'required|string|max:50',
            'kampus_custom'       => 'nullable|string|max:100|required_if:kampus,Lainnya',
            'zona'                => 'required|string|max:255',
            'latitude'            => 'nullable|numeric|between:-90,90',
            'longitude'           => 'nullable|numeric|between:-180,180',
            'usia'                => 'required|string|max:50',
            'gender'              => 'required|string|max:50',
            'warna'               => 'required|string|max:50',
            'warna_custom'        => 'nullable|string|max:100|required_if:warna,Lainnya',
            'bcs'                 => 'required|string|max:50',
            'kondisi_klinis'      => 'nullable|array',
            'kondisi_klinis.*'    => 'string|max:50',
            'panjang_badan_cm'    => 'nullable|numeric|min:0|max:200',
            'panjang_ekor_cm'     => 'nullable|numeric|min:0|max:100',
            'jarak_pakan'         => 'nullable|integer|min:0|max:5000',
            'jenis_pakan'         => 'required|string|max:100',
            'jenis_pakan_custom'  => 'nullable|string|max:150|required_if:jenis_pakan,Lainnya',
            'ancaman'             => 'required|string|max:100',
            'ancaman_custom'      => 'nullable|string|max:150|required_if:ancaman,Lainnya',
            'catatan'             => 'nullable|string|max:2000',

            // Photos validation
            'foto_wajah'          => 'nullable|image|mimes:jpeg,png,jpg,webp|max:8192',
            'foto_atas'           => 'nullable|image|mimes:jpeg,png,jpg,webp|max:8192',
            'foto_samping_kiri'   => 'nullable|image|mimes:jpeg,png,jpg,webp|max:8192',
            'foto_opsional'       => 'nullable|image|mimes:jpeg,png,jpg,webp|max:8192',
            'foto_wajah_cam'      => 'nullable|string',
            'foto_atas_cam'       => 'nullable|string',
            'foto_samping_kiri_cam'=> 'nullable|string',
            'foto_opsional_cam'   => 'nullable|string',
        ]);

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
            'kondisi_klinis'      => $request->kondisi_klinis ?? ['Sehat'],
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
        if ($newFotoWajah = $this->handlePhotoUpload($request, 'foto_wajah', 'foto_wajah_cam')) {
            if ($sensu->foto_wajah) Storage::disk('public')->delete($sensu->foto_wajah);
            $data['foto_wajah'] = $newFotoWajah;
        }

        if ($newFotoAtas = $this->handlePhotoUpload($request, 'foto_atas', 'foto_atas_cam')) {
            if ($sensu->foto_atas) Storage::disk('public')->delete($sensu->foto_atas);
            $data['foto_atas'] = $newFotoAtas;
        }

        if ($newFotoSamping = $this->handlePhotoUpload($request, 'foto_samping_kiri', 'foto_samping_kiri_cam')) {
            if ($sensu->foto_samping_kiri) Storage::disk('public')->delete($sensu->foto_samping_kiri);
            $data['foto_samping_kiri'] = $newFotoSamping;
        }

        if ($newFotoOpsional = $this->handlePhotoUpload($request, 'foto_opsional', 'foto_opsional_cam')) {
            if ($sensu->foto_opsional) Storage::disk('public')->delete($sensu->foto_opsional);
            $data['foto_opsional'] = $newFotoOpsional;
        }

        $sensu->update($data);

        return redirect()->route('volunteer.census.index')
            ->with('success', "Data Sensus Kucing [{$sensu->id_kucing}] berhasil diperbarui!");
    }

    /**
     * Remove the specified census record from storage.
     */
    public function destroy(PtmaCatCensus $sensu)
    {
        // Remove photos
        foreach ([$sensu->foto_wajah, $sensu->foto_atas, $sensu->foto_samping_kiri, $sensu->foto_opsional] as $photo) {
            if ($photo) {
                Storage::disk('public')->delete($photo);
            }
        }

        $idKucing = $sensu->id_kucing;
        $sensu->delete();

        return redirect()->route('volunteer.census.index')
            ->with('success', "Data Sensus Kucing [{$idKucing}] berhasil dihapus.");
    }

    /**
     * Export all census records to CSV format.
     */
    public function exportCsv(Request $request)
    {
        $headers = [
            "Content-type"        => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=Data_Sensus_Kucing_PTMA_" . date('Ymd_His') . ".csv",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $records = PtmaCatCensus::with('volunteer')->latest()->get();

        $callback = function () use ($records) {
            $file = fopen('php://output', 'w');
            
            // Add UTF-8 BOM for Excel compatibility
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($file, [
                'Waktu',
                'ID_Kucing',
                'Kampus',
                'Zona',
                'Latitude',
                'Longitude',
                'Usia',
                'Gender',
                'Warna',
                'BCS',
                'Klinis',
                'Panjang_Badan_cm',
                'Panjang_Ekor_cm',
                'Jarak_Pakan_m',
                'Jenis_Pakan',
                'Ancaman',
                'Catatan',
                'Surveyor'
            ]);

            foreach ($records as $row) {
                fputcsv($file, [
                    $row->created_at ? $row->created_at->format('Y-m-d H:i:s') : '-',
                    $row->id_kucing,
                    $row->display_kampus,
                    $row->zona,
                    $row->latitude ?? '-',
                    $row->longitude ?? '-',
                    $row->usia,
                    $row->gender,
                    $row->display_warna,
                    $row->bcs,
                    is_array($row->kondisi_klinis) ? implode('; ', $row->kondisi_klinis) : ($row->kondisi_klinis ?? 'Sehat'),
                    $row->panjang_badan_cm ?? '-',
                    $row->panjang_ekor_cm ?? '-',
                    $row->jarak_pakan ?? '-',
                    $row->display_jenis_pakan,
                    $row->display_ancaman,
                    $row->catatan ?? '-',
                    $row->volunteer->name ?? 'Relawan'
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Helper to process image file upload or camera base64 data.
     */
    private function handlePhotoUpload(Request $request, string $fileKey, string $camKey): ?string
    {
        // 1. Check direct file upload
        if ($request->hasFile($fileKey)) {
            $file = $request->file($fileKey);
            return $this->compressAndSaveImage(file_get_contents($file->getPathname()));
        }

        // 2. Check base64 camera capture
        if ($request->filled($camKey)) {
            $base64Data = $request->input($camKey);
            if (preg_match('/^data:image\/(\w+);base64,/', $base64Data, $type)) {
                $base64Data = substr($base64Data, strpos($base64Data, ',') + 1);
                $decoded = base64_decode($base64Data);
                if ($decoded) {
                    return $this->compressAndSaveImage($decoded);
                }
            }
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
