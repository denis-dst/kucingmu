<?php

namespace App\Http\Controllers;

use App\Models\RepresentativeRegistration;
use App\Services\IndonesiaWilayahService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class RepresentativeRegistrationController extends Controller
{
    /**
     * Show the registration form for KucingMu Regional Representative.
     */
    public function create()
    {
        $provinces = IndonesiaWilayahService::getProvinces();
        $regenciesMap = IndonesiaWilayahService::getRegenciesMap();
        $user = Auth::user();

        return view('representative.register', compact('provinces', 'regenciesMap', 'user'));
    }

    /**
     * Handle submission of the registration form.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'nbm' => 'required|string|max:50',
            'birth_date' => 'required|date|before:today',
            'email' => 'required|email|max:255',
            'whatsapp_number' => 'required|string|max:30',
            'instagram_username' => 'nullable|string|max:100',
            
            // Regional
            'province_name' => 'required|string|max:100',
            'city_name' => 'required|string|max:100',
            'district_name' => 'required|string|max:100',
            'village_name' => 'required|string|max:100',
            
            // Geo Tagging
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'formatted_address' => 'nullable|string|max:500',
            
            // Organization
            'muhammadiyah_active_leadership' => 'required|string|max:255',
            'sk_pimpinan_document' => 'required|file|mimes:pdf|max:10240',
            'ktam_document' => 'required|file|mimes:jpeg,png,jpg,webp,pdf|max:10240',
            
            // Essay & Consent
            'animal_welfare_essay' => 'required|string|min:30',
            'privacy_agreed' => 'accepted',
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'nbm.required' => 'Nomor Baku Muhammadiyah (NBM) wajib diisi sebagai syarat representatif.',
            'birth_date.required' => 'Tanggal lahir wajib diisi.',
            'birth_date.before' => 'Tanggal lahir tidak valid.',
            'email.required' => 'Alamat email aktif wajib diisi.',
            'email.email' => 'Format alamat email tidak valid.',
            'whatsapp_number.required' => 'Nomor WhatsApp aktif wajib diisi.',
            'province_name.required' => 'Pilih asal provinsi Anda.',
            'city_name.required' => 'Pilih asal kota/kabupaten Anda.',
            'district_name.required' => 'Kecamatan wajib diisi.',
            'village_name.required' => 'Desa/Kelurahan wajib diisi.',
            'muhammadiyah_active_leadership.required' => 'Sebutkan pimpinan Muhammadiyah/Ortom yang sedang aktif Anda ikuti.',
            'sk_pimpinan_document.required' => 'Dokumen SK Pimpinan aktif wajib diunggah dalam format PDF.',
            'sk_pimpinan_document.mimes' => 'File SK Pimpinan harus berformat PDF.',
            'sk_pimpinan_document.max' => 'Ukuran file SK Pimpinan maksimal 10 MB.',
            'ktam_document.required' => 'Tangkapan layar / foto KTAM Fisik atau Aplikasi MASA wajib diunggah.',
            'ktam_document.mimes' => 'File KTAM harus berformat JPG, PNG, WEBP, atau PDF.',
            'ktam_document.max' => 'Ukuran file KTAM maksimal 10 MB.',
            'animal_welfare_essay.required' => 'Uraian wawasan kesrawan/animal welfare wajib diisi.',
            'animal_welfare_essay.min' => 'Uraian wawasan kesrawan minimal 30 karakter.',
            'privacy_agreed.accepted' => 'Anda wajib menyetujui pernyataan privasi & komitmen data organisasi.',
        ]);

        // Upload SK Pimpinan Document (PDF)
        $skPimpinanPath = null;
        if ($request->hasFile('sk_pimpinan_document')) {
            $skFile = $request->file('sk_pimpinan_document');
            $skFilename = 'sk_pimpinan_' . time() . '_' . Str::random(8) . '.' . $skFile->getClientOriginalExtension();
            $skPimpinanPath = $skFile->storeAs('representatives/sk_pimpinan', $skFilename, 'public');
        }

        // Upload KTAM Document (Image / PDF)
        $ktamPath = null;
        if ($request->hasFile('ktam_document')) {
            $ktamFile = $request->file('ktam_document');
            $ktamFilename = 'ktam_' . time() . '_' . Str::random(8) . '.' . $ktamFile->getClientOriginalExtension();
            $ktamPath = $ktamFile->storeAs('representatives/ktam', $ktamFilename, 'public');
        }

        // Format Instagram username (remove @ prefix if entered)
        $igUsername = $validated['instagram_username'] ? ltrim(trim($validated['instagram_username']), '@') : null;

        // Clean WhatsApp number
        $cleanWhatsapp = preg_replace('/[^0-9+]/', '', $validated['whatsapp_number']);

        // Create registration record
        $registration = RepresentativeRegistration::create([
            'user_id' => Auth::id(),
            'name' => trim($validated['name']),
            'nbm' => trim($validated['nbm']),
            'birth_date' => $validated['birth_date'],
            'email' => strtolower(trim($validated['email'])),
            'whatsapp_number' => $cleanWhatsapp,
            'instagram_username' => $igUsername,
            
            'province_name' => $validated['province_name'],
            'city_name' => $validated['city_name'],
            'district_name' => trim($validated['district_name']),
            'village_name' => trim($validated['village_name']),
            
            'latitude' => $validated['latitude'] ?? null,
            'longitude' => $validated['longitude'] ?? null,
            'formatted_address' => $validated['formatted_address'] ?? null,
            
            'muhammadiyah_active_leadership' => trim($validated['muhammadiyah_active_leadership']),
            'sk_pimpinan_document_path' => $skPimpinanPath,
            'ktam_document_path' => $ktamPath,
            
            'animal_welfare_essay' => trim($validated['animal_welfare_essay']),
            'privacy_agreed' => true,
            'status' => 'pending',
        ]);

        return redirect()->route('representative.success', ['number' => $registration->registration_number])
            ->with('registered_success', true);
    }

    /**
     * Show success page with registration details.
     */
    public function success(string $number)
    {
        $registration = RepresentativeRegistration::where('registration_number', $number)->firstOrFail();

        return view('representative.success', compact('registration'));
    }

    /**
     * API endpoint to get regencies by province.
     */
    public function getRegencies(Request $request)
    {
        $province = $request->query('province');
        if (!$province) {
            return response()->json([]);
        }

        $regencies = IndonesiaWilayahService::getRegencies($province);
        return response()->json($regencies);
    }
}
