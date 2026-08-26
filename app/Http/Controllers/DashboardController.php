<?php

namespace App\Http\Controllers;

use App\Models\Cat;
use App\Models\CatPhoto;
use App\Models\Appointment;
use App\Models\MedicalRecord;
use App\Models\KtamCard;
use App\Models\MasterWilayah;
use App\Models\User;
use App\Services\KtamService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class DashboardController extends Controller
{
    /**
     * Show the application dashboard based on user role.
     */
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ($user->isAdmin()) {
            return $this->adminDashboard();
        } elseif ($user->isDokter()) {
            return $this->dokterDashboard();
        } elseif ($user->isVolunteer()) {
            return $this->volunteerDashboard();
        } else {
            return $this->memberDashboard();
        }
    }

    /**
     * Render Admin Dashboard.
     */
    protected function adminDashboard()
    {
        $stats = [
            'cats_count' => Cat::count(),
            'appointments_count' => Appointment::count(),
            'records_count' => MedicalRecord::count(),
            'ktam_count' => KtamCard::count(),
            'pending_verification_count' => Cat::whereDoesntHave('ktamCard')->count(),
        ];

        $cats = Cat::with(['owner', 'ktamCard', 'photos', 'medicalRecords.vet'])->latest()->paginate(10);
        $pendingVerificationCats = Cat::whereDoesntHave('ktamCard')
            ->with(['owner', 'photos', 'medicalRecords.vet'])
            ->latest()
            ->get();
            
        $appointments = Appointment::with('cat')->orderBy('date', 'desc')->take(5)->get();

        return view('admin.dashboard', compact('stats', 'cats', 'pendingVerificationCats', 'appointments'));
    }

    /**
     * Render Vet Dashboard.
     */
    protected function dokterDashboard()
    {
        // Queue for today
        $queue = Appointment::with('cat.owner')
            ->whereIn('status', ['scheduled', 'checked_in'])
            ->whereDate('date', Carbon::today())
            ->orderBy('status', 'desc') // checked_in first
            ->orderBy('id', 'asc')
            ->get();

        $recentRecords = MedicalRecord::with('cat', 'appointment')
            ->where('vet_id', Auth::id())
            ->latest()
            ->take(10)
            ->get();

        return view('dokter.dashboard', compact('queue', 'recentRecords'));
    }

    /**
     * Render Volunteer Dashboard.
     */
    protected function volunteerDashboard()
    {
        $todayAppointments = Appointment::with('cat.owner')
            ->whereDate('date', Carbon::today())
            ->orderBy('id', 'desc')
            ->get();

        return view('volunteer.dashboard', compact('todayAppointments'));
    }

    /**
     * Render Member Dashboard.
     */
    protected function memberDashboard()
    {
        $cats = Auth::user()->cats()->with(['ktamCard', 'medicalRecords', 'photos', 'wilayah'])->get();
        $appointments = Appointment::whereIn('cat_id', $cats->pluck('id'))
            ->with('cat')
            ->latest()
            ->get();

        $activeEvents = \App\Models\Event::where('status', 'active')->orderBy('date', 'asc')->get();
        $masterWilayahs = MasterWilayah::getActiveList();

        return view('member.dashboard', compact('cats', 'appointments', 'activeEvents', 'masterWilayahs'));
    }

    /**
     * Store a new cat profile (for Member).
     */
    public function storeCat(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'breed' => 'required|string|max:255',
            'gender' => 'required|in:male,female',
            'date_of_birth' => 'required|date',
            'wilayah_code' => 'nullable|string|max:10',
            'color' => 'nullable|string|max:100',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'photos.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'photo_labels.*' => 'nullable|string|max:255',
            'primary_photo_index' => 'nullable|integer',
            'biometric_type' => 'nullable|in:none,paw,nose,both',
            'biometric_photo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'biometric_code' => 'nullable|string|max:255',
            'allergies' => 'nullable|string',
            'vaccine_history' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $photoPath = $this->compressAndStorePhoto($file);
        }

        $biometricPhotoPath = null;
        if ($request->hasFile('biometric_photo')) {
            $file = $request->file('biometric_photo');
            $biometricPhotoPath = $this->compressAndStorePhoto($file, 'biometrics');
        }

        $wilayahCode = $request->wilayah_code ?: '34';

        $cat = Cat::create([
            'user_id' => Auth::id(),
            'name' => $request->name,
            'breed' => $request->breed,
            'gender' => $request->gender,
            'date_of_birth' => $request->date_of_birth,
            'wilayah_code' => $wilayahCode,
            'color' => $request->color,
            'photo_path' => $photoPath,
            'biometric_type' => $request->biometric_type ?? 'none',
            'biometric_photo_path' => $biometricPhotoPath,
            'biometric_code' => $request->biometric_code,
            'allergies' => $request->allergies,
            'vaccine_history' => $request->vaccine_history,
            'notes' => $request->notes,
        ]);

        if ($photoPath) {
            CatPhoto::create([
                'cat_id' => $cat->id,
                'photo_path' => $photoPath,
                'label' => 'Tampak Depan',
                'is_primary' => true,
            ]);
        }

        if ($request->hasFile('photos')) {
            $uploadedPhotos = $request->file('photos');
            $labels = $request->input('photo_labels', []);
            $primaryIdx = (int) $request->input('primary_photo_index', -1);

            foreach ($uploadedPhotos as $index => $uploadedFile) {
                $savedPath = $this->compressAndStorePhoto($uploadedFile);
                $label = isset($labels[$index]) && !empty($labels[$index]) ? $labels[$index] : 'Foto ' . ($index + 1);
                $isPrimary = ($index === $primaryIdx) || (!$photoPath && $index === 0);

                if ($isPrimary && $photoPath) {
                    CatPhoto::where('cat_id', $cat->id)->update(['is_primary' => false]);
                    $cat->update(['photo_path' => $savedPath]);
                }

                CatPhoto::create([
                    'cat_id' => $cat->id,
                    'photo_path' => $savedPath,
                    'label' => $label,
                    'is_primary' => $isPrimary,
                ]);
            }
        }

        return redirect()->route('dashboard')->with('success', 'Profil kucing berhasil dibuat.');
    }

    /**
     * Show the edit cat profile form (for Member).
     */
    public function editCat(Cat $cat)
    {
        if ((int) $cat->user_id !== (int) Auth::id() && !Auth::user()->isAdmin()) {
            abort(403);
        }

        $cat->load(['photos', 'wilayah']);
        $masterWilayahs = MasterWilayah::getActiveList();

        return view('member.edit-cat', compact('cat', 'masterWilayahs'));
    }

    /**
     * Update the cat profile (for Member/Admin).
     */
    public function updateCat(Request $request, Cat $cat)
    {
        if ((int) $cat->user_id !== (int) Auth::id() && !Auth::user()->isAdmin()) {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'breed' => 'required|string|max:255',
            'gender' => 'required|in:male,female',
            'date_of_birth' => 'required|date',
            'wilayah_code' => 'nullable|string|max:10',
            'color' => 'nullable|string|max:100',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'photos.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'photo_labels.*' => 'nullable|string|max:255',
            'biometric_type' => 'nullable|in:none,paw,nose,both',
            'biometric_photo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'biometric_code' => 'nullable|string|max:255',
            'allergies' => 'nullable|string',
            'vaccine_history' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $photoPath = $cat->photo_path;
        if ($request->hasFile('photo')) {
            if ($cat->photo_path) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($cat->photo_path);
            }
            $file = $request->file('photo');
            $photoPath = $this->compressAndStorePhoto($file);

            CatPhoto::where('cat_id', $cat->id)->update(['is_primary' => false]);
            CatPhoto::create([
                'cat_id' => $cat->id,
                'photo_path' => $photoPath,
                'label' => 'Tampak Depan',
                'is_primary' => true,
            ]);
        }

        $biometricPhotoPath = $cat->biometric_photo_path;
        if ($request->hasFile('biometric_photo')) {
            if ($cat->biometric_photo_path) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($cat->biometric_photo_path);
            }
            $file = $request->file('biometric_photo');
            $biometricPhotoPath = $this->compressAndStorePhoto($file, 'biometrics');
        }

        $oldWilayah = $cat->wilayah_code;
        $newWilayah = $request->wilayah_code ?: ($oldWilayah ?: '34');
        $uniqueCode = $cat->unique_code;

        if (empty($uniqueCode) || $oldWilayah !== $newWilayah) {
            $uniqueCode = Cat::generateUniqueCode($newWilayah, $cat->id);
        }

        $cat->update([
            'name' => $request->name,
            'breed' => $request->breed,
            'gender' => $request->gender,
            'date_of_birth' => $request->date_of_birth,
            'wilayah_code' => $newWilayah,
            'unique_code' => $uniqueCode,
            'color' => $request->color,
            'photo_path' => $photoPath,
            'biometric_type' => $request->biometric_type ?? $cat->biometric_type,
            'biometric_photo_path' => $biometricPhotoPath,
            'biometric_code' => $request->biometric_code ?? $cat->biometric_code,
            'allergies' => $request->allergies,
            'vaccine_history' => $request->vaccine_history,
            'notes' => $request->notes,
        ]);

        if ($request->hasFile('photos')) {
            $uploadedPhotos = $request->file('photos');
            $labels = $request->input('photo_labels', []);

            foreach ($uploadedPhotos as $index => $uploadedFile) {
                $savedPath = $this->compressAndStorePhoto($uploadedFile);
                $label = isset($labels[$index]) && !empty($labels[$index]) ? $labels[$index] : 'Foto Samping/Lain';

                $hasPrimary = CatPhoto::where('cat_id', $cat->id)->where('is_primary', true)->exists();

                CatPhoto::create([
                    'cat_id' => $cat->id,
                    'photo_path' => $savedPath,
                    'label' => $label,
                    'is_primary' => !$hasPrimary,
                ]);

                if (!$hasPrimary) {
                    $cat->update(['photo_path' => $savedPath]);
                }
            }
        }

        return redirect()->route('dashboard')->with('success', 'Profil kucing berhasil diperbarui.');
    }

    /**
     * Upload an individual photo for cat gallery.
     */
    public function uploadCatPhoto(Request $request, Cat $cat)
    {
        if ((int) $cat->user_id !== (int) Auth::id() && !Auth::user()->isAdmin()) {
            abort(403);
        }

        $request->validate([
            'photo' => 'required|image|mimes:jpeg,png,jpg,webp|max:5120',
            'label' => 'required|string|max:255',
            'is_primary' => 'nullable|boolean',
        ]);

        $photoPath = $this->compressAndStorePhoto($request->file('photo'));
        $isPrimary = $request->boolean('is_primary');

        if ($isPrimary) {
            CatPhoto::where('cat_id', $cat->id)->update(['is_primary' => false]);
            $cat->update(['photo_path' => $photoPath]);
        } else {
            $hasPrimary = CatPhoto::where('cat_id', $cat->id)->where('is_primary', true)->exists();
            if (!$hasPrimary) {
                $isPrimary = true;
                $cat->update(['photo_path' => $photoPath]);
            }
        }

        CatPhoto::create([
            'cat_id' => $cat->id,
            'photo_path' => $photoPath,
            'label' => $request->label,
            'is_primary' => $isPrimary,
        ]);

        return redirect()->back()->with('success', 'Foto kucing berhasil ditambahkan ke galeri.');
    }

    /**
     * Set a photo as primary for KTAM.
     */
    public function setPrimaryPhoto(CatPhoto $photo)
    {
        $cat = $photo->cat;
        if ((int) $cat->user_id !== (int) Auth::id() && !Auth::user()->isAdmin()) {
            abort(403);
        }

        CatPhoto::where('cat_id', $cat->id)->update(['is_primary' => false]);
        $photo->update(['is_primary' => true]);

        $cat->update(['photo_path' => $photo->photo_path]);

        return redirect()->back()->with('success', 'Foto utama KTAM berhasil diperbarui.');
    }

    /**
     * Delete a photo from cat gallery.
     */
    public function deletePhoto(CatPhoto $photo)
    {
        $cat = $photo->cat;
        if ((int) $cat->user_id !== (int) Auth::id() && !Auth::user()->isAdmin()) {
            abort(403);
        }

        $wasPrimary = $photo->is_primary;

        if ($photo->photo_path) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($photo->photo_path);
        }

        $photo->delete();

        if ($wasPrimary) {
            $nextPrimary = CatPhoto::where('cat_id', $cat->id)->first();
            if ($nextPrimary) {
                $nextPrimary->update(['is_primary' => true]);
                $cat->update(['photo_path' => $nextPrimary->photo_path]);
            } else {
                $cat->update(['photo_path' => null]);
            }
        }

        return redirect()->back()->with('success', 'Foto berhasil dihapus.');
    }

    /**
     * Book a health checkup appointment (for Member).
     */
    public function storeAppointment(Request $request)
    {
        $request->validate([
            'cat_id' => 'required|exists:cats,id',
            'date' => 'required|date|after_or_equal:today',
            'time_slot' => 'required|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $cat = Cat::findOrFail($request->cat_id);
        if ((int) $cat->user_id !== (int) Auth::id()) {
            abort(403);
        }

        Appointment::create([
            'cat_id' => $request->cat_id,
            'date' => $request->date,
            'time_slot' => $request->time_slot,
            'status' => 'scheduled',
            'notes' => $request->notes,
        ]);

        return redirect()->route('dashboard')->with('success', 'Jadwal pemeriksaan berhasil dibuat.');
    }

    /**
     * Store clinical check-up (for Dokter).
     * Dokter only records medical findings. KTAM is issued separately via Admin verification.
     */
    public function storeCheckup(Request $request, Appointment $appointment)
    {
        $request->validate([
            'weight' => 'required|numeric|min:0',
            'temperature' => 'required|numeric|min:0',
            'general_condition' => 'required|string|max:255',
            'deworming_given' => 'nullable|boolean',
            'anti_flea_given' => 'nullable|boolean',
            'supplement_given' => 'nullable|boolean',
            'treatment_notes' => 'nullable|string',
            'recommendation' => 'nullable|string',
        ]);

        MedicalRecord::create([
            'appointment_id' => $appointment->id,
            'cat_id' => $appointment->cat_id,
            'vet_id' => Auth::id(),
            'weight' => $request->weight,
            'temperature' => $request->temperature,
            'general_condition' => $request->general_condition,
            'deworming_given' => $request->has('deworming_given'),
            'anti_flea_given' => $request->has('anti_flea_given'),
            'supplement_given' => $request->has('supplement_given'),
            'treatment_notes' => $request->treatment_notes,
            'recommendation' => $request->recommendation,
        ]);

        $appointment->update(['status' => 'completed']);

        return redirect()->route('dashboard')->with('success', 'Rekam medis berhasil disimpan. Data kucing masuk ke antrian Verifikasi Admin untuk penerbitan KTAM.');
    }

    /**
     * Verify cat data & issue KTAM Card (for Admin).
     */
    public function verifyAndIssueKtam(Request $request, Cat $cat, KtamService $ktamService)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403);
        }

        $card = $ktamService->issueCard($cat, Auth::id());

        return redirect()->back()->with('success', 'Kartu KTAM (' . $card->ktam_number . ') berhasil terverifikasi & diterbitkan oleh Admin.');
    }

    /**
     * Download digital KTAM Card as PDF.
     */
    public function downloadKtam(Cat $cat)
    {
        $cat->load(['photos', 'owner', 'wilayah']);
        $card = $cat->ktamCard;
        if (!$card) {
            return redirect()->route('dashboard')->with('error', 'Kucing ini belum memiliki kartu KTAM.');
        }

        if (Auth::user()->role === 'member' && (int) $cat->user_id !== (int) Auth::id()) {
            abort(403);
        }

        $pdf = Pdf::loadView('pdf.ktam', compact('card', 'cat'));
        $pdf->setPaper([0, 0, 243.78, 153.07], 'portrait');
        
        return $pdf->stream('ktam_' . str_replace(' ', '_', strtolower($cat->name)) . '.pdf');
    }

    /**
     * Preview Draft KTAM.
     */
    public function previewKtam(Cat $cat)
    {
        $cat->load(['photos', 'owner', 'wilayah']);

        if (Auth::user()->role === 'member' && (int) $cat->user_id !== (int) Auth::id()) {
            abort(403);
        }

        $card = $cat->ktamCard;
        if (!$card) {
            $card = new \stdClass();
            $card->ktam_number = $cat->formatted_unique_code ?? 'DRAFT-XXX-XXX';
            $card->qr_code_payload = asset('images/logo-muhammadiyah.svg'); 
        }

        $isDraft = true;

        return view('pdf.ktam', compact('card', 'cat', 'isDraft'));
    }

    /**
     * Fast check-in (for Volunteer).
     */
    public function checkInAppointment(Appointment $appointment)
    {
        $appointment->update(['status' => 'checked_in']);
        return redirect()->route('dashboard')->with('success', 'Check-in berhasil. Kucing masuk ke antrian dokter.');
    }

    /**
     * Register a new member & cat at location (for Volunteer/Admin).
     */
    public function quickRegister(Request $request)
    {
        $request->validate([
            'owner_name' => 'required|string|max:255',
            'owner_email' => 'required|email|unique:users,email',
            'owner_phone' => 'required|string|max:255',
            'owner_nbm' => 'nullable|string|max:255',
            'cat_name' => 'required|string|max:255',
            'cat_breed' => 'required|string|max:255',
            'cat_gender' => 'required|in:male,female',
            'cat_dob' => 'required|date',
            'wilayah_code' => 'nullable|string|max:10',
            'color' => 'nullable|string|max:100',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'biometric_type' => 'nullable|in:none,paw,nose,both',
            'biometric_photo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'biometric_code' => 'nullable|string|max:255',
        ]);

        $owner = User::create([
            'name' => $request->owner_name,
            'email' => $request->owner_email,
            'password' => bcrypt('kucingmu123'),
            'phone' => $request->owner_phone,
            'role' => 'member',
            'muhammadiyah_id' => $request->owner_nbm,
        ]);

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $photoPath = $this->compressAndStorePhoto($file);
        }

        $biometricPhotoPath = null;
        if ($request->hasFile('biometric_photo')) {
            $file = $request->file('biometric_photo');
            $biometricPhotoPath = $this->compressAndStorePhoto($file, 'biometrics');
        }

        $cat = Cat::create([
            'user_id' => $owner->id,
            'name' => $request->cat_name,
            'breed' => $request->cat_breed,
            'gender' => $request->cat_gender,
            'date_of_birth' => $request->cat_dob,
            'wilayah_code' => $request->wilayah_code ?: '34',
            'color' => $request->color,
            'photo_path' => $photoPath,
            'biometric_type' => $request->biometric_type ?? 'none',
            'biometric_photo_path' => $biometricPhotoPath,
            'biometric_code' => $request->biometric_code,
        ]);

        if ($photoPath) {
            CatPhoto::create([
                'cat_id' => $cat->id,
                'photo_path' => $photoPath,
                'label' => 'Tampak Depan',
                'is_primary' => true,
            ]);
        }

        Appointment::create([
            'cat_id' => $cat->id,
            'date' => Carbon::today(),
            'time_slot' => 'On-site Registration',
            'status' => 'checked_in',
            'notes' => 'Registrasi langsung di lokasi event.',
        ]);

        return redirect()->route('dashboard')->with('success', 'Registrasi berhasil. Kucing langsung masuk antrian dokter.');
    }

    /**
     * KTAM Verification Page (Public scan landing).
     */
    public function verifyKtam($number)
    {
        $card = KtamCard::where('ktam_number', $number)
            ->orWhereHas('cat', function($q) use ($number) {
                $q->where('unique_code', $number);
            })
            ->with('verifier')
            ->first();

        if (!$card) {
            $cat = Cat::where('unique_code', $number)->firstOrFail();
            $card = $cat->ktamCard ?? new KtamCard([
                'ktam_number' => $cat->formatted_unique_code,
                'issue_date' => $cat->created_at,
            ]);
        } else {
            $cat = $card->cat()->with(['owner', 'photos', 'wilayah'])->first();
        }

        $records = MedicalRecord::where('cat_id', $cat->id)->with('vet')->latest()->get();

        return view('ktam-verify', compact('card', 'cat', 'records'));
    }

        $records = MedicalRecord::where('cat_id', $cat->id)->with('vet')->latest()->get();

        return view('ktam-verify', compact('card', 'cat', 'records'));
    }

    /**
     * Export all data to CSV format (for Admin).
     */
    public function exportData()
    {
        if (!Auth::user()->isAdmin()) {
            abort(403);
        }

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=kucingmu_database_export.csv",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $cards = KtamCard::with(['cat.owner', 'verifier'])->get();

        $callback = function() use($cards) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['KTAM Number', 'Cat Name', 'Breed', 'Gender', 'Biometric Type', 'Owner Name', 'Owner NBM', 'Owner Phone', 'Issue Date', 'Verified By']);

            foreach ($cards as $card) {
                fputcsv($file, [
                    $card->ktam_number,
                    $card->cat->name,
                    $card->cat->breed,
                    $card->cat->gender,
                    strtoupper($card->cat->biometric_type ?? 'NONE'),
                    $card->cat->owner->name,
                    $card->cat->owner->muhammadiyah_id ?? '-',
                    $card->cat->owner->phone ?? '-',
                    $card->issue_date->format('Y-m-d'),
                    $card->verifier->name ?? 'System/Admin',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Synchronize offline registration and checkup queue (for Volunteer).
     */
    public function syncOffline(Request $request)
    {
        $rawEntries = $request->input('entries', $request->input('items', []));
        $request->merge(['entries' => $rawEntries]);

        $request->validate([
            'entries' => 'required|array',
            'entries.*.owner_name' => 'required|string',
            'entries.*.owner_email' => 'required|email',
            'entries.*.owner_phone' => 'required|string',
            'entries.*.owner_nbm' => 'nullable|string',
            'entries.*.cat_name' => 'required|string',
            'entries.*.cat_breed' => 'required|string',
            'entries.*.cat_gender' => 'required|in:male,female',
            'entries.*.cat_dob' => 'required|date',
        ]);

        $syncedCount = 0;
        foreach ($request->entries as $entry) {
            $owner = User::where('email', $entry['owner_email'])->first();
            if (!$owner) {
                $owner = User::create([
                    'name' => $entry['owner_name'],
                    'email' => $entry['owner_email'],
                    'password' => bcrypt('kucingmu123'),
                    'phone' => $entry['owner_phone'],
                    'role' => 'member',
                    'muhammadiyah_id' => $entry['owner_nbm'],
                ]);
            }

            $cat = Cat::create([
                'user_id' => $owner->id,
                'name' => $entry['cat_name'],
                'breed' => $entry['cat_breed'],
                'gender' => $entry['cat_gender'],
                'date_of_birth' => $entry['cat_dob'],
            ]);

            Appointment::create([
                'cat_id' => $cat->id,
                'date' => Carbon::today(),
                'time_slot' => 'Offline Field Mode',
                'status' => 'checked_in',
                'notes' => 'Disinkronkan dari antrian offline lapangan.',
            ]);

            $syncedCount++;
        }

        return response()->json([
            'success' => true,
            'message' => $syncedCount . ' data antrian offline berhasil disinkronkan.',
        ]);
    }

    /**
     * Compress and store an uploaded photo using native PHP GD.
     * Scales down to max 800x800, converts to JPEG, compresses under 512KB.
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @param string $dir
     * @return string The relative storage path (e.g. 'cats/xxxxx.jpg')
     */
    private function compressAndStorePhoto($file, string $dir = 'cats'): string
    {
        $binary = file_get_contents($file->getPathname());
        $sourceImage = imagecreatefromstring($binary);

        if (!$sourceImage) {
            throw new \RuntimeException('Gagal membaca file gambar. Pastikan file adalah gambar yang valid.');
        }

        $origWidth = imagesx($sourceImage);
        $origHeight = imagesy($sourceImage);

        $maxDim = 800;
        $newWidth = $origWidth;
        $newHeight = $origHeight;

        if ($origWidth > $maxDim || $origHeight > $maxDim) {
            $ratio = min($maxDim / $origWidth, $maxDim / $origHeight);
            $newWidth = (int) round($origWidth * $ratio);
            $newHeight = (int) round($origHeight * $ratio);
        }

        $resized = imagecreatetruecolor($newWidth, $newHeight);
        $white = imagecolorallocate($resized, 255, 255, 255);
        imagefill($resized, 0, 0, $white);

        imagecopyresampled($resized, $sourceImage, 0, 0, 0, 0, $newWidth, $newHeight, $origWidth, $origHeight);
        imagedestroy($sourceImage);

        $filename = $dir . '/' . uniqid() . '.jpg';
        $fullPath = storage_path('app/public/' . $filename);
        if (!file_exists(dirname($fullPath))) {
            mkdir(dirname($fullPath), 0755, true);
        }

        imagejpeg($resized, $fullPath, 70);
        imagedestroy($resized);

        return $filename;
    }
}
